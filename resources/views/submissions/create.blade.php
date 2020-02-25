@extends('layouts.app')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
@endsection

@section('title_menu')
<th>Submissions</th>
@if ($user_id != 'all' and !in_array( Auth::user()->role->name, ['student'])) 
	<a href="{{route('submissions.index', [$assignment_id, 'all', $problem_id, 'all'])}}">Remove filter user</a>
@endif
@if ($problem_id != 'all')
	<a href="{{route('submissions.index', [$assignment_id, $user_id, 'all', 'all'])}}">Remove filter problem</a>
@endif
@endsection

@section('content')
<a href="{{route('submissions.index', [$assignment_id, $user_id, $problem_id, 'all'])}}">All</a>
<a href="{{route('submissions.index', [$assignment_id, $user_id, $problem_id, 'final'])}}">Final</a>
<div class="row">
    <div class="col">
        <div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>#</th>
						<th>ID</th>
						<th>Username</th>
						<th>Name</th>
						<th>Problem</th>
						<th>Submit Time</th>
						<th>Score</th>
						<th>Delay %</th>
						<th>Lang</th>
						<th>Status</th>
						<th>Code</th>
						<th>Log</th>
						<th>Rejudge</th>
					</tr>
				</thead>
				@foreach ($submissions as $submission)
				<tr data-id="{{$submission->id}}">
					<td>{{$loop->iteration}} </td>
					<td>{{$submission->id}}</td>
					<td><a href="{{route('submissions.index', [$assignment_id, strval($submission->user_id), $problem_id, 'all'])}}">
						{{$submission->user->username}}
					</a></td>
					<td>{{$submission->user->display_name}}</td>
					<td>
						<a href="{{route('problems.show', $submission->problem_id)}}">{{$submission->problem->name}}</a>
						<a href="{{route('submissions.index', [$assignment_id, $user_id, strval($submission->problem_id), 'all'])}}">Filter</a>
					</td>
					<td>{{$submission->time}}</td>
					<td>{{$submission->score}}</td>
					<td>Delay</td>
					<td>{{$submission->language->name}}</td>
					<td>Status</td>
					<td>Code</td>
					<td>Log</td>
					<td>Rejudge</td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>
@endsection

@section('body_end')
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function () {
    $("table").DataTable({
		"pageLength": 10,
		"lengthMenu": [ [10, 20, 30, 50, -1], [10, 20, 30, 50, "All"] ]
	});
});
</script>
@endsection