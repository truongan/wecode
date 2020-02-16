@extends('layouts.app')

@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}
    <span class="title_menu_item"><a href="{!! route('assignments.create') !!}"><i class="fa fa-plus color8"></i>Add</a></span>
@endsection

@section('content')

<form method="POST"  action="{!! route('assignments.store') !!}">
<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
<table>
	<tr>
			<td>Assignment Name:</td>
			<td><input type="text" name="name"></td>
	</tr>
	<tr>
			<td>Description:</td>
			<td><input type="text" name="description"></td>
	</tr>
	<tr>
			<td>Start time:</td>
			<td><input type="date" name="start_time_date"> <input type="time" name="start_time_time"></td>
	</tr>
	<tr>
			<td>Finish time:</td>
			<td><input type="date" name="finish_time_date"> <input type="time" name="finish_time_time"></td>
	</tr>
	<tr>
			<td>Extra time:</td>
			<td><input type="number" name="extra_time"></td>
	</tr>
	<tr>
			<td>Participants:</td>
			<td><input type="number" name="participants"></td>
	</tr>
	<tr>
			<td>PDF File:</td>
			<td><input type="file" name="participants"></td>
	</tr>
	<tr>
			<td>Open:</td>
		 	<td><input type="checkbox" name="open"></td>
	</tr>
	<tr>
		 	<td>Scoreboard:</td>
		 	<td><input type="checkbox" name="score_board"></td>
	</tr>
	<tr>
			<td>Coefficient rule:</td>
			<td><input type="text" name="late_rule"></td>
	</tr>

</table>
<button class="btn btn-primary"   type="submit" > OK </button>
</form>
@endsection

