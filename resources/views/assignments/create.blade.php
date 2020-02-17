@extends('layouts.app')

@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('title_menu')
@endsection

@section('content')
<form method="POST"  
	@if (Route::currentRouteName() == 'assignments.edit')
		action="{!! route('assignments.update', $assignment) !!}"
	@else  
		action="{!! route('assignments.store') !!}"
	@endif
enctype="multipart/form-data">
@if (Route::currentRouteName() == 'assignments.edit')
	@method("PUT")
@endif
<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
<table>
	<tr>
			<td>Assignment Name:</td>
			<td><input type="text" name="name" value="{{$assignment->name ?? ''}}"></td>
	</tr>
	<tr>
			<td>Description:</td>
			<td><input type="text" name="description" value="{{$assignment->description ?? ''}}"></td>
	</tr>
	<tr>
			<td>Start time:</td>
			<td>
				<input type="date" name="start_time_date" value="{{empty($assignment) ? '' : date('Y-m-d', strtotime($assignment->start_time))}}"> 
				<input type="time" name="start_time_time" value="{{empty($assignment) ? '' : date('H:i', strtotime($assignment->start_time))}}">
			</td>
	</tr>
	<tr>
			<td>Finish time:</td>
			<td>
				<input type="date" name="finish_time_date" value="{{empty($assignment) ? '' : date('Y-m-d', strtotime($assignment->finish_time))}}"> 
				<input type="time" name="finish_time_time" value="{{empty($assignment) ? '' : date('H:i', strtotime($assignment->start_time))}}">
			</td>
	</tr>
	<tr>
			<td>Extra time:</td>
			<td><input type="number" name="extra_time" value="{{$assignment->extra_time ?? ''}}"></td>
	</tr>
	<tr>
			<td>Participants:</td>
			<td><input type="text" name="participants" value="{{$assignment->participants ?? ''}}"></td>
	</tr>
	<tr>
			<td>PDF File:</td>
			<td><input type="file" name="pdf_file" value="Choose.pdf"></td>
	</tr>
	<tr>
			<td>Open:</td>
		 	<td><input type="checkbox" name="open" 
		 		@if (!empty($assignment))
			 		@if ($assignment->open)
			 			checked
			 		@endif
			 	@endif
		 	></td>
	</tr>
	<tr>
		 	<td>Scoreboard:</td>
		 	<td><input type="checkbox" name="score_board" 
		 		@if (!empty($assignment))
			 		@if ($assignment->score_board)
			 			checked
			 		@endif
			 	@endif
		 	></td>
	</tr>
	<tr>
			<td>Coefficient rule:</td>
			<td><input type="text" name="late_rule" value="{{$assignment->late_rule ?? ''}}"></td>
	</tr>

</table>
<button class="btn btn-primary"   type="submit" > OK </button>
</form>
@endsection

