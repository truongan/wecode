@php($selected = 'notifications')
@extends('layouts.app')
@section('head_title','New Notification')
@section('icon', 'fas fa-plus')

@section('title', 'New Notification')

@section('body_end')
<script type="text/javascript">
    mathjax_path = "{{ asset('assets/MathJax-2.7.9') }}/MathJax.js?config=TeX-MML-AM_CHTML"
</script>
<script src="{{ asset('assets/ckeditor/ckeditor.js') }}" charset="utf-8"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){
	CKEDITOR.replace("notif_text");
});
</script>
@endsection

@section('content')
<form method="POST"  action="{!! route('notifications.store') !!}">
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