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
{{-- notifications/({{$notif_edit}} ? 'edit/'+{{$notif_edit.id}} : 'add' ) --}}
{!! Form::open(['method' => "POST", 'route' => 'notifications.store']) !!}
{{-- @if ($notif_edit ?? '')
	<input type="hidden" name="id" value="{{ $notif_edit.id }}"/>
@endif --}}

<p class="input_p">
	<label for="form_title" class="tiny">Title:</label>
	<input id="form_title" name="title" type="text" class="sharif_input" value=" $notif_edit.title "/>
</p>
<p class="input_p">
	<label for="notif_text" class="tiny">Text:</label><br><br>
	<textarea id="notif_text" name="text"> $notif_edit.text </textarea>
</p>
<p class="input_p">
	<input type="submit" value=" $notif_edit ? 'Save' : 'Add' " class="sharif_input"/>
</p>
{!! Form::close() !!}
@endsection