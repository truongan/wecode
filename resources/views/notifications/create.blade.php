@php($selected = 'notifications')
@extends('layouts.app')
@section('head_title','New Clarification request')
@section('icon', 'fas fa-plus')

@section('title', 'New Clarification request')

@section('body_end')
<script src="{{ asset('assets/ckeditor/ckeditor.js') }}" charset="utf-8"></script>
<script>
$(document).ready(function(){
	CKEDITOR.replace("notif_text");
});
</script>
@endsection

@section('content')
<form method="POST"  action="{!! route('notifications.store') !!}">
	@if ($all_users != null)
		<label for="form_title" class="tiny">Select recipent:</label>
		<select class="form-select" aria-label="Default select example" name = "recipent_id">
			<option value="0"> ALL USERS</option>
			@foreach ($all_users as $id => $name )
			<option value="{{$id}}"> {{$name}}</option>
			@endforeach

		</select>
	
	@endif 
	<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
	<p class="input_p">
		<label for="form_title" class="tiny">Title:</label>
		<input id="form_title" name="title" type="text" class="sharif_input"/>
	</p>
	<p class="input_p">
		<label for="notif_text" class="tiny">Description:</label>
		<input type="text" name="description"></>
	</p>
	
	<p class="input_p">
		<label for="notif_text" class="tiny">Text:</label>
		<textarea id="notif_text" name="text"></textarea>
	</p>
	<p class="input_p">
		<input type="submit" value="Add" class="btn btn-primary"/>
	</p>
</form>	
@endsection