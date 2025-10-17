@extends('layouts.app')
@section('head_title','New assignments')
@section('icon', 'fas fa-plus-square')

@section('title', 'New assignments')

@section('other_assets')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/select2/select2.min.css') }}">
<style>
	#choice_multi_assignment .select2-selection__choice{
		display:none !important;
	}
	.problem-score{
		width:8em!important;
	}
	.select2-container  textarea {
		color: black;
	}
</style>
@endsection

@section('title_menu')
<span class="ms-4 fs-6">
	<a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/add_assignment.md" target="_blank"><i class="fa fa-question-circle text-danger"></i> Help</a>
	@if (Route::currentRouteName() == 'assignments.edit')
	<a href="{{ route('submissions.index', ['assignment_id' => $assignment->id, 'problem_id' => 'all', 'user_id' =>'all', 'choose' => 'all']) }}"> <i class="fa fa-list color2"></i> Submissions list</a>
	@endif
</span>
@endsection

@section('body_end')
<script type="text/javascript" src="{{ asset('assets/js/Sortable.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/select2/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/add_assignments.js') }}"></script>
<script type='text/javascript' src="{{ asset('assets/js/taboverride.min.js') }}"></script>
<script>
	document.addEventListener("DOMContentLoaded", function(){
		tabOverride.set(document.getElementsByTagName('textarea'));
		$('.js-example-basic-multiple').select2();
	});
</script>
<script type="text/javascript">
	shj.num_of_problems={{ count($problems) }};
</script>

@endsection

@section('content')


<div class="ms-n2 me-n1">
	{{-- {% set msgclasses = {'text-success': 'text-success', 'text-info': 'text-warning', 'text-danger': 'text-danger'} %} --}}
	{{-- {% for message in messages %}
		<p class="{{ msgclasses[message.type] }}">{{ message.text }}</p>
	{% endfor %} --}}

	{{-- {% if edit %}
	<p>
		<i class="fa fa-info-circle fa-lg color8"></i> If you don't want to change tests or pdf file, just do not upload its file.
	</p>
	{% endif %} --}}
	@php($edit = Route::currentRouteName() == 'assignments.edit')
	<form method="POST" class = "gy-5"
		@if (Route::currentRouteName() == 'assignments.edit')
			action="{{ route('assignments.update', $assignment) }}"
		@else
			action="{{ route('assignments.store') }}"
		@endif
	enctype="multipart/form-data"
	>
		@if (Route::currentRouteName() == 'assignments.edit')
			@method("PUT")
		@endif
		@csrf
		{{-- <input type="hidden" name="number_of_problems" id="nop" value="{{ $edit ? $assignment->problems->count() : $problems->count() }}"/> --}}
		<div class="row">
			<div class="col-sm-6 ">
				<div class="form-old-row row gy-2">
					<div class="col-sm-4">
						<label for="form_a_name">Assignment Name</label>
					</div>
					<div class="col-sm-8">
						<input id="form_a_name" type="text" name="name" class="form-control col-xs-7" value="{{ $edit ? $assignment->name : old('name') }}"/>
						@error('name')
							<div class="alert alert-danger">{{ $message }} </div>
						@enderror
					</div>

					<div class="col-sm-4">
						<label for="start_time">Start Time</label>
					</div>
					<div class="col-sm-8">
						<input id="start_time" type="hidden" name="start_time" class="form-control" value="" />
						<div class="form-old-row row">
							<div class="col-xl-7">
								<input id="start_time_date" type="date" name="start_time_date" class="form-control" value="{{ $edit ? $assignment->start_time->setTimezone($settings['timezone'])->isoFormat('Y-MM-DD')  : old('start_time_date') }}" />
							</div>
							<div class="col-xl-5">
								<input id="start_time_time" type="time" name="start_time_time" class="form-control" value="{{ $edit ? $assignment->start_time->setTimezone($settings['timezone'])->isoFormat('HH:mm')  : old('start_time_time',0) }}" />
							</div>
						</div>
						{{-- {{ form_error('start_time', '<div class="alert alert-danger">', '</div>') }} --}}
					</div>

					<div class="col-sm-4">
						<label for="finish_time">Finish Time
						<small class="form-text text-muted">Set finish time before start time will set the deadline to <strong>FOREVER</strong></small>
						</label>
					</div>
					<div class="col-sm-8">
						<input id="finish_time" type="hidden" name="finish_time" class="form-control" value="" />
						<div class="form-old-row row">
							<div class="col-xl-7">
								<input id="finish_time_date" type="date" name="finish_time_date" class="form-control" value="{{ $edit ? $assignment->finish_time->setTimezone($settings['timezone'])->isoFormat('Y-MM-DD') : old('finish_time_date') }}" />
							</div>
							<div class="col-xl-5">
								<input id="finish_time_time" type="time" name="finish_time_time" class="form-control" value="{{ $edit ? $assignment->finish_time->setTimezone($settings['timezone'])->isoFormat('HH:mm') : old('finish_time_time') }}" />
							</div>
						</div>
						{{-- {{ form_error('finish_time', '<div class="alert alert-danger">', '</div>') }} --}}
					</div>

					<div class="col-sm-4">
						<label for="form_extra_time">
						Extra Time (seconds)
						<small class="form-text text-muted">Extra time for late submissions, multiply operator allowed</small>
						</label>
					</div>
					<div class="col-sm-8">
						<input id="form_extra_time" type="text" name="extra_time" id="extra_time" class="form-control" value="{{ $edit ? $assignment->extra_time : old('extra_time', '0*60*60') }}" />
						{{-- {{ form_error('extra_time', '<div class="alert alert-danger">', '</div>') }} --}}
					</div>

					<div class="col-sm-4" data-bs-toggle="tooltip" data-bs-placement="top" title="If your classes have to take assignment with different start and/or finish time, please craete different assignments" >
						<label>Select class(es)<br>
							<small class="form-text text-muted">Select the classes that are required to take this assignment.</small>
						</label>
					</div>
					<div class="col-sm-8">
						<select class="js-example-basic-multiple form-control" multiple="multiple" name="lop_id[]">
							@foreach( $all_lops as $p)
							<option value="{{ $p->id }}" data-name="{{$p->name}}"
								{{ isset($lops[$p->id]) ? 'selected="selected"' : ''  }}
							>
								{{$p->name}}
								{{-- ( {{ $p->instructors->pluck('username')->join(', ')}}) --}}
								({{$p->users->first()->username}})
							</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-4">
						<label for="form_pdf">PDF File<br>
							<small class="form-text text-muted">If the assignment has an additional PDF description file upload it here. </small>
						</label>
					</div>
					<div class="col-sm-8">
						<input id="form_pdf" type="file" name="pdf" class="form-control"/>
					</div>
				</div>
			</div>

			<div class="col-sm-6">
				<label for="form_description">Description</label>
				<textarea id="form_description" name="description" rows="2" class="form-control add_text">{{ $edit ? $assignment->description : old('description', '') }}</textarea>
				{{-- {{ form_error('late_rule', '<div class="alert alert-danger">', '</div>') }} --}}

				<div class="form-check form-switch mt-2">
					<input id="form_a_open" class="form-check-input" type="checkbox" name="open" value="1" {!! $edit ? ($assignment->open ? 'checked' : '') :'' !!} />
					<label for="form_a_open" class="form-check-label">Open</label>
					<small class="form-text text-muted ">Open or close this assignment for submission</small>
				</div>
				{{-- {{ form_error('open', '<div class="alert alert-danger">', '</div>') }} --}}

				<div class="form-check form-switch my-2">
					<input id="form_a_scoreboard" class="form-check-input" type="checkbox" name="scoreboard" value="1" {!! $edit ? ($assignment->score_board ? 'checked' : '') : '' !!} />
					<label for="form_a_scoreboard" class="form-check-label">Scoreboard</label>
				<small class="form-text text-muted mb-3 ">Check this to publish scoreboard to student, lecturer can always see scoreboard</small>
				</div>
				{{-- {{ form_error('scoreboard', '<div class="alert alert-danger">', '</div>') }} --}}

				<label for="form_late_rule">Coefficient rule (<a target="_blank" href="https://symfony.com/doc/current/reference/formats/expression_language.html">Expression</a>)
					<small class="form-text text-muted  "> to calculate score coefficient (in percentage) based on <code>extra_time</code>, <code>delay</code> and <code>submit_time</code></small>
				</label>
				<input type="text" id="form_late_rule" name="late_rule" rows="4" class="form-control add_text" value="{{ $edit ? $assignment->late_rule : old('late_rule', $settings['default_late_rule'], false) }}"/>
				{{-- {{ form_error('late_rule', '<div class="alert alert-danger">', '</div>') }} --}}

				<div class="mt-2 row">
					<div class="col-sm-4 ">
						<label>
							Limit language <small class="form-text text-muted">if your problems support many languages, you can limit the languages that can be used in this assigment </small>
						</label>
					</div>

					<div class="col-sm-8">
						<select name="language_ids[]" class="form-select"  multiple aria-label="Further limit allow language for this assignments">
							@foreach ($all_languages as $lang)
								<option value="{{ $lang->id }}"
									@if (!$edit ||  in_array($lang->id , explode(", ", $assignment->language_ids) ))
										selected
									@endif
								>{{$lang->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="row my-3">
			<div class="col-sm-8" id="choice_multi_assignment">
				<label> Select problem(s) for this assignment
					<small class="form-text text-muted">You can type in the box below to search for problems
					</small>
				</label>
				<select class="all_problems form-control" multiple="multiple">
					@foreach( $all_problems as $p)
					<option value="{{ $p->id }}" data-name="{{$p->name}}" data-sharable="{{$p->sharable}}" data-id="{{$p->id}}" data-tags="{{ $p->tags->implode('text', ', ')}}" data-note="{{ $p->admin_note }}" data-no_of_assignment="{{ $p->assignments_count }}"  data-owner="{{ $p->user->username ?? 'none'}}"
						{{ isset($problems[$p->id]) ? 'selected="selected"' : ''  }}
						>
					{{$p->id}} - {{$p->name}} ({{ $p->user->username ?? 'none'}} |  {{ $p->tags->implode('text', ', ') }}  | {{   $p->admin_note }}) </option>
					@endforeach
				</select>
			</div>
			<div class="col-sm-4">
				<label for="min_assignment_to_select">Select all problems</label>
				<div class="input-group input-group-sm">
					<span class="input-group-text" >used in less than</span>
					<input type="number" class="form-control" value="2" id="multiple_problems_min" >
					<span class="input-group-text" >assignments</span>
					<button class="btn btn-outline-primary" type="button" id="select_multiple_problems">Add</button>
				</div>
				<small id="helpId" class="text-muted">Use this to select every problems that have been used in no more than a specific number of assignments. You can still fine tuning the selection afterward </small>
			</div>
		</div>

	<label> Set alias, score and order for problems in this assignment
		<small class="form-text text-muted">Problem's alias will be displayed when student view this assignment instead of the problem's original name<br/>
		You can drag the handle to re-order the problems.<br/>
		Remove one problem from assignment won't remove the submissions of that problem but will reset its alias and score to default if you re-add it later.
		<br/><input class="ms-auto" type="number" id="score_amount" value="100">
			<button  class="m-2 btn btn-info btn-sm" id="distribute_score" type="button" >Distribute score</button>
			<button class="btn btn-info btn-sm" id="set_score" type="button">Set score all</button>
		<br/>Currently: <span class="badge bg-info count_problems">0</span> problems with a total score of <span class="badge bg-info sum_score">0</span>
		</small>
	</label>
	<ul id="problem_list" class="list-group">
		@php($i = 0)
		@foreach($problems as $problem)
		<li   class="list-group-item {{$problem->id == -1 ? 'd-none' : ''}} "><div class="row align-items-center">
			<div class="col-auto list_handle pointer">
				<span><i class="fa fa-grip-vertical fa-lg fa-fw"></i></span>
			</div>
			<div class="col">
				<div class="row  row-cols-auto align-items-center" >
					{{-- <div class="input-group"> --}}
						<input type="hidden" name="problem_id[]" value="{{$problem->id}}"/>
						<div class="col lead me-2">
							{{-- @php(dd($problem->user->username)); --}}
							<span class="badge text-dark bg-light">{{ $problem->id }}</span>
							<span class="badge bg-secondary rounded-pill">{{ $problem->user->username ??'none' }}</span>
							{{ $problem->name }}
						</div>
						<div class="col">
							<div class="input-group input-group-sm me-2">
								<label class="input-group-text">Alias</label>
								<input type="text" name="problem_name[]" class="form-control " value="{{ $problem->pivot->problem_name }}"/>
							</div>
						</div>
						<div class="col">
							<div class="input-group input-group-sm me-2">
								<label class="input-group-text">Score</label>
								<input type="number" name="problem_score[]" class="form-control problem-score" value="{{ $problem->pivot->score }}"/>
							</div>
						</div>
						<span class="text-muted admin_note">{{ $problem->admin_note }}</span>
					{{-- </div> --}}
				</div>
			</div>
			<div class="col-auto">
					<button class="btn btn-danger list_remover"><span><i class="fa fa-times-circle fa-lg fa-fw pointer"></i></span></button>
			</div>
			@php($i = $i+1)
		</div></li>
		@endforeach
	</ul>

	<div class="mt-4">
		<input type="submit" value="{{ $edit ? "Edit" : "Add" }} Assignment" class="sharif_input btn btn-primary"/>
	</div>
	</form>
</div>
@endsection
