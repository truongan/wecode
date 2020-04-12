@extends('layouts.app')
@section('head_title','Assignments')
@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('other_assets')
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
<script>
	if(!!window.performance && window.performance.navigation.type === 2)
	{
		window.location.reload();
	}
</script>
@endsection
@if (!in_array( Auth::user()->role->name, ['student']))
@section('title_menu')
<small><nav class="nav nav-pills">
	<a class="nav-link" href="{{ route('assignments.create') }}"><i class="fa fa-plus color8"></i> Add</a>
	<a class="nav-link active" href="{{ url('assignments/') }}"><i class="far fa-star color1"></i>Assingments setting</a>
	<a class="nav-link" href="{{ url('assignments/scores/accepted') }}"><i class="far fa-star color1"></i>Assignments score accepted</a>
	<a class="nav-link" href="{{ url('assignments/scores/sum') }}"><i class="far fa-star color1"></i>Assignments score olp</a>
</nav></small>
@endsection
@endif
@section('content')
<div class="row">
	<table class="wecode_table table table-striped table-bordered">
		<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>Select</th>
				<th>Name</th>
				<th>Submissions</th>
				<th>Coefficient</th>
				<th>Start Time</th>
				<th>Finish Time</th>
				<th>Scoreboard</th>
				<th>PDF</th>
				<th>Status</th>
				@if (!in_array( Auth::user()->role->name, ['student']))
					<th>Action</th>
				@endif
			</tr>
		</thead>
		
		@foreach ($assignments as $assignment)
		<tr data-id="{{$assignment->id}}">
			<td>{{$loop->iteration}} </td>
			<td>
				<span data-toggle="tooltip" title="View an assignment's problem or submission will set it as your default assignment">
					<i  class=" far {{ (isset(Auth::user()->selected_assignment->id) && $assignment->id == Auth::user()->selected_assignment->id) ? 'fa-check-square color6' : 'fa-square' }} fa-2x" data-id="{{ $assignment->id }}"></i>
				</span>
			</td>
			<td>
				<a href="{{ route('assignments.show',['assignment'=>$assignment,'problem_id'=>$assignment->problems->first()->id??0]) }}" data-toggle="tooltip" title="Click to view problem(s)">
					<strong>{{ $assignment->name }}</strong>
					<br/>
					({{ $assignment->no_of_problems }} problems)
				</a>
			</td>
			<td>
				@if ( in_array( Auth::user()->role->name, ['student']) )
					<a href="{{ route('submissions.index', [$assignment->id, Auth::user()->id, 'all', 'all'])}}" data-toggle="tooltip" title="View all submissions">
						<small>{{$assignment->total_submits}} submission{{ $assignment->total_submits > 1 ? 's' : ''}}</small>
					</a>
				@else
					<a href="{{ route('submissions.index', [$assignment->id, 'all', 'all', 'all'])}}" data-toggle="tooltip" title="View all submissions">
						<small>{{$assignment->total_submits}} submission{{ $assignment->total_submits > 1 ? 's' : ''}}</small>
					</a>
				@endif
			</td>
			<td>
				@if ($assignment->finished)
					<span style="color: red;">Finished</span>
				@else
					@if($assignment->coefficient != "error")
						{{$assignment->coefficient}}%
					@else
						<span style="color: red;">! Error</span>
					@endif
				@endif
			</td>
			<td>{{$assignment->start_time}}</td>
			<td>{{$assignment->finish_time}}</td>
			<td>
				@if ($assignment->score_board)
					<a href="{{ url("scoreboard/full/$assignment->id")}}" data-toggle="tooltip" title="Click to viewa assignment's scoreboard">View<i class="fas fa-external-link-alt"></i></a>
				@else
					<a href="{{ url("scoreboard/full/$assignment->id")}}"  data-toggle="tooltip" title="Scoreboard closed, admin view only" ><span class="text-secondary">View<i class="fas fa-external-link-alt "></i></span></a>
				@endif
			</td>
			<td>
				<a href="{{ url("assignments/pdf/$assignment->id") }}"><i class="far fa-lg fa-file-pdf"></i></a>
			</td>
			<td>
				@if ($assignment->open)
					<span class="text-success">Open</span>
				@else
					<span class="text-danger">Close</span>
				@endif
			</td>
			@if (!in_array( Auth::user()->role->name, ['student']))
			<td>
				<a title="Edit" href="{{ route('assignments.edit', $assignment) }}"><i class="fas fa-edit fa-lg color9"></i></a>
			</td>
			@endif
		</tr>
		@endforeach
	</table>
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