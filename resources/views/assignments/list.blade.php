@extends('layouts.app')

@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
@endsection

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}
    <span class="title_menu_item"><a href="{!! route('assignments.create') !!}"><i class="fa fa-plus color8"></i>Add</a></span>
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
					<td>{{$assignment->id}}</td>
					<td>{{$assignment->name}}</td>
					<td>{{$assignment->total_submits}}</td>
					<td>{{$assignment->late_rule}}</td>
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
$(document).ready(function () {
    $("table").DataTable({
		"pageLength": 10,
		"lengthMenu": [ [10, 20, 30, 50, -1], [10, 20, 30, 50, "All"] ]
	});
});
</script>
@endsection