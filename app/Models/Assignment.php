<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
class my_expression_language_provider implements ExpressionFunctionProviderInterface
{
	public function getFunctions(): array
	{
		$func_list = ['abs', 'acos', 'acosh', 'asin', 'asinh', 'atan', 'atan2', 'atanh', 'base_convert', 'bindec', 'ceil', 'cos', 'cosh', 'decbin', 'dechex', 'decoct', 'deg2rad', 'exp', 'expm1', 'fdiv', 'floor', 'fmod', 'hexdec', 'hypot', 'intdiv', 'is_finite', 'is_infinite', 'is_nan', 'log', 'log10', 'log1p', 'max', 'min', 'octdec', 'pi', 'pow', 'rad2deg', 'round', 'sin', 'sinh', 'sqrt', 'tan', 'tanh'] ;


		return array_map(array('Symfony\Component\ExpressionLanguage\ExpressionFunction','fromPhp'), $func_list);
	}
}

class Assignment extends Model
{
	//
	protected $fillable = [
		"name",
		"total_submits",
		"open",
		"score_board",
		"javaexceptions",
		"start_time",
		"finish_time",
		"extra_time",
		"late_rule",
		"participants",
		"description",
		"user_id",
		"language_ids",
	];
	protected $casts = [
		"start_time" => "datetime",
		"finish_time" => "datetime",
	];

	public function problems()
	{
		return $this->belongsToMany("App\Models\Problem")
			->withPivot("score", "ordering", "problem_name")
			->withTimestamps();
	}
	public function user()
	{
		return $this->belongsTo("App\Models\User");
	}

	public function submissions()
	{
		return $this->hasMany("App\Models\Submission");
	}

	public function lops()
	{
		return $this->belongsToMany("App\Models\Lop");
	}

	public function scoreboard()
	{
		return $this->hasOne("App\Models\Scoreboard");
	}

	public function cannot_edit(User $actor)
	{
		// dd($actor->role->name);
		if ($actor->role->name == "admin") {
			return false;
		} elseif ($actor->role->name == "head_instructor") {
			if (
				$this->user->id != $actor->id &&
				!$actor
					->lops()
					->with("assignments")
					->get()
					->pluck("assignments")
					->collapse()
					->pluck("id")
					->contains($this->id)
			) {
				return "You can only edit assignment you created or assignment belongs to one of your classes";
			} else {
				return false;
			}
		} else {
			return "You do not have permission to edit assignment";
		}
	}

	public function can_submit(User $user, Problem $problem = null)
	{
		$result = new class {};
		$result->error_message = "Unknown error";
		$result->can_submit = false;

		//2021-09-08 : An's note: leave it here till i found somewhere better
		// dd('shit');
		if (
			$user->trial_time &&
			in_array($user->role->name, ["student"]) &&
			$user->created_at->addHours($user->trial_time) <= Carbon::now()
		) {
			$user->role_id = 5; //Hopefully 5 mean guest.
			$user->save();
		}

		if (in_array($user->role->name, ["guest"])) {
			$result->error_message =
				" Guest can not make submissions. Contact site admin to upgrade your account ";
		} elseif (
			$this->id == 0 &&
			!in_array($user->role->name, ["admin"]) && // Admin can submit to practice assignment on any problem
			($problem == null || $problem->can_practice($user) == false)
		) {
			$result->error_message =
				"You don't have permission to practice with this problem";
		} elseif (
			in_array($user->role->name, ["student"]) &&
			$this->open == 0
		) {
			// if assignment is closed, non-student users (admin, instructors) still can submit
			$result->error_message =
				" You cannot submit, selected assignment is closed.";
		} elseif (
			!$this->started() &&
			in_array($user->role->name, ["student"])
		) {
			// non-student users can submit to not started assignments
			$result->error_message =
				"You cannot submit, Selected assignment has not started.";
		} elseif (
			$this->start_time < $this->finish_time &&
			Carbon::now() > $this->finish_time->addSeconds($this->extra_time)
		) {
			// deadline = finish_time + extra_time
			// but if start time is before finish time, the deadline is NEVER
			$result->error_message =
				"You cannot submit, Selected assignment has finished.";
		} elseif (!$this->is_participant($user)) {
			$result->error_message =
				"You cannot submit, You are not registered for submitting.";
		} else {
			$result->error_message = "none";
			$result->can_submit = true;
		}
		return $result;
	}

	public function is_participant($user)
	{
		if ($this->id == 0) {
			return true;
		}
		if (in_array($user->role->name, ["admin"])) {
			return true;
		}
		return in_array(
			$user->id,
			$this->lops
				->pluck("users")
				->collapse()
				->pluck("id")
				->unique()
				->toArray(),
		);
	}

	public function started()
	{
		return strtotime(date("Y-m-d H:i:s")) >= strtotime($this->start_time); //now should be larger than start time
	}

	private function _eval_coefficient($delay = 0, $submit_time = 0)
	{
		try {
			$extra_time = $this->extra_time;
			$expressionLanguage = new ExpressionLanguage();
			$expressionLanguage->registerProvider(new my_expression_language_provider());

			$coefficient = $expressionLanguage->evaluate($this->late_rule, [
				'delay' => $delay,
				'extra_time' => $extra_time,
			]);

			$coefficient = round($coefficient, 1);
			if ($coefficient < 0)
				$coefficient = max(-10000, $coefficient);
			else
				$coefficient = min(10000, $coefficient);

		} catch (\Throwable $e) {
			// dd($e);
			$coefficient = "error";
		}
		if (!isset($coefficient) || !is_numeric($coefficient)) {
			$coefficient = "error";
		}
		return $coefficient;
	}

	public function update_submissions_coefficient()
	{
		foreach ($this->submissions as $sub) {
			$delay = $this->finish_time->diffInSeconds($sub->created_at);
			$submit_time = $this->start_time->diffInSeconds($sub->created_at);

			$sub->coefficient = $this->_eval_coefficient($delay, $submit_time);
			$sub->save();
		}
	}
	public function eval_coefficient()
	{
		$delay = $this->finish_time->diffInSeconds(Carbon::now());
		$submit_time = $this->start_time->diffInSeconds(Carbon::now());
		return $this->_eval_coefficient($delay, $submit_time);
	}

	public function is_finished()
	{
		$delay = $this->finish_time->diffInSeconds(Carbon::now());
		return $this->start_time < $this->finish_time &&
			$delay > $this->extra_time;
	}

	public static function assignment_info($assignment_id)
	{
		$query = Assignment::where("id", $assignment_id);
		if ($query->count() != 1) {
			return [
				"id" => 0,
				"name" => "instructors'submit",
				"finish_time" => 0,
				"extra_time" => 0,
				"problems" => 0,
				"open" => 0,
				"total_submits" => $query->submissions->count(),
			];
		}

		return $query->first();
	}

	// Reset final submissions choices
	public function reset_final_submission_choices()
	{
		$problem_score = $this->problems->pluck("pivot.score", "id");
		$subs = $this->submissions()->oldest()->get()->keyBy("id");

		$final_subs = [];
		foreach ($subs as $sub) {
			$key = $sub->user_id . "," . $sub->problem_id;
			$sub->is_final = 0;
			$change = true;
			if (isset($final_subs[$key])) {
				$final = $subs[$final_subs[$key]];

				$final_score = ceil(
					($final->pre_score *
						($problem_score[$final->problem_id] ?? 0)) /
						10000,
				);
				$final_score = ceil(
					$final_score *
						($final->coefficient === "error"
							? 0
							: $final->coefficient / 100),
				);

				$sub_score = ceil(
					($sub->pre_score *
						($problem_score[$sub->problem_id] ?? 0)) /
						10000,
				);
				$sub_score = ceil(
					$sub_score *
						($sub->coefficient === "error"
							? 0
							: $sub->coefficient / 100),
				);

				if ($sub->pre_score == 10000) {
					if (
						$final->pre_score == 10000 &&
						$sub_score <= $final_score
					) {
						$change = false;
					}
				} else {
					if ($final->pre_score == 10000) {
						$change = false;
					} elseif ($sub_score <= $final_score) {
						$change = false;
					}
				}
				if ($change) {
					$final->is_final = 0;
					$final->save();
				}
			}
			if ($change) {
				$final_subs[$key] = $sub->id;
				$sub->is_final = 1;
			}
			$sub->save();
		}
	}
}
