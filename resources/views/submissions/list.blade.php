@extends('layouts.app')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
@endsection

@section('content')

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
				@if ($final == 0)
					@foreach ($submissions as $submission)
				@else 
					@foreach ($submissions->where('is_final',1)->get() as $submission)
				@endif
				<tr data-id="{{$submission->id}}">
					<td>{{$loop->iteration}} </td>
					<td>{{$submission->id}}</td>
					<td>{{$submission->user->username}}</td>
					<td>{{$submission->user->display_name}}</td>
					<td>{{$submission->problem->name}}</td>
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