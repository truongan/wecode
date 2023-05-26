@php($selected = 'resolver')
@extends('layouts.app')
@section('icon', 'fas fa-snowflake')
@section('head_title', 'Resolver')
@section('title', 'Resolver')

@section('other_assets')
    <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css' />
    <script>
        if (!!window.performance && window.performance.navigation.type === 2) {
            window.location.reload();
        }
    </script>
@endsection

@section('title_menu')

    @php($sl = 0)
    @if (isset(Auth::user()->selected_assignment_id))
        @php($sl = 1)
    @endif
@endsection


@section('content')
    <div class="mx-n2">
        <h1>resolver</h1>
        {{-- TABLE --}}
        <table id="wecode_leaderboard" class="wecode_table table table-striped table-bordered table-sm">
            <thead class="thead-old table-dark">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Score</th>
                    @foreach ($problem_id as $ordering => $id)
                        <th>
							{{ chr ($ordering + 65) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
			<tbody></tbody>
{{-- 
			@foreach ($data['username'] as $index => $username)
				<tr>
					<td>{{}}</td>
					<td></td>
					<td></td>
					<td></td>
				</tr> --}}

{{-- 
            @foreach ($scoreboard['username'] as $i => $sc_username)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td><a
                            href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => 'all', 'user_id' => $scores[$sc_username]['id'], 'choose' => 'all']) }}">{{ $sc_username }}</a>
                    </td>
                    <td>{{ $names[$sc_username] }}</td>
                    <td>{{ $scoreboard['lops'][$sc_username] ?? 'none' }}</td>
                    <td>

                        <span>{{ $scoreboard['score'][$loop->index] }}</span>
                        <p class="excess">
                            <span class="small"
                                title="Total Time + Submit Penalty">{{ $scoreboard['submit_penalty'][$loop->index]->cascade()->forHumans(['short' => true]) }}</span>
                        </p>
                    </td>
                    <td class="text-dark">
                        <span class="lead"><strong>{{ $scoreboard['accepted_score'][$loop->index] }}</strong></span>
                        <p class="excess">
                            <span class="small"
                                title="Solved : Attack ratio">{{ $scoreboard['solved'][$loop->index] }}:{{ $scoreboard['tried_to_solve'][$loop->index] }}</span>
                        </p>
                    </td>
                    @foreach ($problems as $problem)
                        @if (isset($scores[$sc_username][$problem->id]['score']))
                            @if ($scores[$sc_username][$problem->id]['score'] == 100)
                                <td class="bg-success">
                                @else
                                <td class="bg-danger">
                            @endif
                            <a href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => $scores[$sc_username]['id'], 'choose' => 'all']) }}"
                                class="lead text-white">
                                {{ $scores[$sc_username][$problem->id]['score'] }}
                            </a>
                            <p class="excess">
                                <span class="small text-white" title="Total tries and time to final submit">
                                    {{ $number_of_submissions[$sc_username][$problem->id] }}
                                    -
                                </span>

                                @if ($scores[$sc_username][$problem->id]['late']->totalSeconds > 0)
                                    <span
                                        class="text-white">{{ $scores[$sc_username][$problem->id]['late']->forHumans(['short' => true]) }}</span>
                                @else
                                    <span
                                        class="small text-white">{{ $scores[$sc_username][$problem->id]['time']->forHumans(['short' => true]) }}</span>
                                @endif
                            </p>
                            </td>
                        @else
                            <td>-</td>
                        @endif
                    @endforeach




                </tr>
            @endforeach
            <tfoot class="bg-dark text-light">
                <th colspan="6">Sumarry</th>
                @foreach ($problems as $problem)
                    <th>
                        <a class="small"
                            href="{{ route('assignments.show', ['assignment' => $assignment_id, 'problem_id' => $problem->id]) }}">{{ $problem->pivot->problem_name }}</a>
                        <br>
                        <a class="text-light"
                            href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => 'all', 'choose' => 'final']) }}">{{ $problem->pivot->score }}</a>
                    </th>
                @endforeach

                <tr class="bg-dark text-light">
                    <td colspan="6">Solved/tries</td>
                    @foreach ($problems as $p)
                        <td>
                            {{ $stat_print[$p->id]->solved_tries }}
                        </td>
                    @endforeach
                </tr>
                <tr class="bg-dark text-light">
                    <td colspan="6">Solved users/tries users/Total users</td>
                    @foreach ($problems as $p)
                        <td>
                            {{ $stat_print[$p->id]->solved_tries_users }}
                        </td>
                    @endforeach
                </tr>
                <tr class="bg-dark text-light">
                    <td colspan="6">Average tries per users</td>
                    @foreach ($problems as $p)
                        <td>
                            {{ $stat_print[$p->id]->average_tries }}
                        </td>
                    @endforeach
                </tr>
                <tr class="bg-dark text-light">
                    <td colspan="6">Average tries to solve</td>
                    @foreach ($problems as $p)
                        <td>
                            {{ $stat_print[$p->id]->average_tries_2_solve }}
                        </td>
                    @endforeach
                </tr>
            </tfoot> --}}
        </table> 

        {{-- TABLE --}}
    </div>
@endsection

@section('body_end')
<script>
	// Variables
	const php_problem_id = @json($problem_id)
	// Need to enter here
	const php_accepted_time = @json($accepted_time)
	// Need to enter here
	const php_accepted = @json($accepted)
	// Need to enter here
	const php_tries = @json($tries)
	// Need to enter here
	const php_data = @json($data)
	// Need to enter here

	// Get user list
	let users_list = []
	for (let i = 0; i < php_data['username'].length; i++) {
		const user = {
			username: php_data['username'][i],
			total_accepted: php_data['accepted'][i],
			total_accepted_time: php_data['accepted_time'][i],
		}

		user['accepted_time'] = php_accepted_time[user.username]
		user['accepted'] = php_accepted[user.username]
		user['tries_before'] = php_tries[user.username]['tries_before']
		user['tries_during'] = php_tries[user.username]['tries_during']

		users_list.push(user)
	}
	console.log(users_list)
	// console.log(accepted_time)
	

	// Generate row from user list
	const generateUserResultCell = (user, php_problem_id) => {
		return php_problem_id.map((problem_id) => {
			console.log(user)
			console.log(user.tries_during)

			if (user.tries_during[problem_id] > 0) {
				return ('<td class="bg-warning">' + user.tries_before[problem_id] + '+' + user.tries_during[problem_id] + ' tries' + '</td>')
			} else {
				if (user.tries_before[problem_id] == 0) {
					return ('<td>' + '-' + '</td>')
				} else if (user.accepted_time[problem_id] == 0) {
					return ('<td class="bg-danger">' + user.accepted_time[problem_id] + '</td>')
				} else {
					return ('<td class="bg-success">' + user.accepted_time[problem_id] + '</td>')
				}
			}
		})
	}

	for (let i = 0; i < users_list.length; i++) {
		const row = $(
			'<tr class="user_row">' +
				'<td class="rank">' + (i + 1) + '</td>' +
				'<td class="name">' + users_list[i].username + '</td>' +
				'<td class="score">' + users_list[i].total_accepted_time + '</td>' +
				(generateUserResultCell(users_list[i], php_problem_id)) +
			'</tr>'
		)
		users_list[i].row = row
		$("#wecode_leaderboard > tbody").append(row)
		
	}
	// console.log(users_list)

</script>
@endsection
