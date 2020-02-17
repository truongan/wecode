@extends('layouts.app')

@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}
    <span class="title_menu_item"><a href="{!! route('assignments.create') !!}"><i class="fa fa-plus color8"></i>Add</a></span>
@endsection

@section('content')
<table>
	<tr>
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
	@foreach ($assignments as $assignment)
	<tr>
		<th>{{$assignment->id}}</th>
		<th>{{$assignment->name}}</th>
		<th>{{$assignment->total_submits}}</th>
		<th>{{$assignment->late_rule}}</th>
		<th>{{$assignment->start_time}}</th>
		<th>{{$assignment->finish_time}}</th>
		<th>{{$assignment->score_board}}</th>
		<th>File PDF</th>
		<th>{{$assignment->open}}</th>
		<th>
			<a href="{{ route('assignments.edit', $assignment) }}">Edit</a>
		</th>
	</tr>
	@endforeach
</table>
@endsection