@php($selected = 'notifications')
@extends('layouts.app')

@section('icon', 'fas fa-plus')

@section('title', 'New Notification')

@section('body_end')
<script src="{{ asset('assets/ckeditor/ckeditor.js') }}" charset="utf-8"></script>
<script>
$(document).ready(function(){
	CKEDITOR.replace("notif_text");
});
</script>
@endsection

@section('content')
<form method="POST"  action="{!! route('problems.store') !!}">
	<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
	<input type="file" id="myfile" name="myfile"><br><br>
	<p class="input_p">
		<input type="submit" value="Add" class="btn btn-primary"/>
	</p>
</form>	
@endsection