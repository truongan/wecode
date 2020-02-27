@extends('layouts.app')

@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
@endsection

@section('title_menu')
<small><nav class="nav nav-pills">
	<a class="nav-link" href="{{ route('assignments.create') }}"><i class="fa fa-plus color8"></i> Add</a>
	<a class="nav-link active" href="{{ url('assignments/') }}"><i class="far fa-star color1"></i>Assingments setting</a>
	<a class="nav-link" href="{{ url('assignments/scores/accepted') }}"><i class="far fa-star color1"></i>Assignments score accepted</a>
	<a class="nav-link" href="{{ url('assignments/scores/sum') }}"><i class="far fa-star color1"></i>Assignments score olp</a>
</nav></small>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div class="table-responsive">
			<table class="table table-striped table-bordered">
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
						<th>Action</th>
					</tr>
				</thead>
				
				@foreach ($assignments as $assignment)
				<tr data-id="{{$assignment->id}}">
					<td>{{$loop->iteration}} </td>
					<td>
						<span data-toggle="tooltip" title="View an assignment's problem or submission will set it as your default assignment">
							<i  class=" far {{ $assignment->id == isset(Auth::user()->selected_assignment->id) ? 'fa-check-square color6' : 'fa-square' }} fa-2x" data-id="{{ $assignment->id }}"></i>
						</span>
					</td>
					<td>
						<a href="{{ url("view_problem/$assignment->id") }}" data-toggle="tooltip" title="Click to view problem(s)"><strong>{{ $assignment->name }}</strong><br/>({{ $assignment->no_of_problems }} problems)</a>
					</td>
					<td>
						@if ( in_array( Auth::user()->role->name, ['student']) )
							<a href="{{ route('submissions.index', [$assignment->id, Auth::user()->id, 'all', 'all'])}}">{{$assignment->total_submits}}</a>
						@else
							<a href="{{ route('submissions.index', [$assignment->id, 'all', 'all', 'all'])}}">{{$assignment->total_submits}}</a>
						@endif
					</td>
					<td>{{$assignment->coefficient}}</td>
					<td>{{$assignment->start_time}}</td>
					<td>{{$assignment->finish_time}}</td>
					<td>{{$assignment->score_board}}</td>
					<td>File PDF</td>
					<td>{{$assignment->open}}</td>
					<td>
						<a title="Edit" href="{{ route('assignments.edit', $assignment) }}"><i class="fas fa-edit fa-lg color9"></i></a>
					</td>
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
{{-- $(document).ready(function () {
    $("table").DataTable({
		"pageLength": 10,
		"lengthMenu": [ [10, 20, 30, 50, -1], [10, 20, 30, 50, "All"] ]
	});
}); --}}
</script>
@endsection