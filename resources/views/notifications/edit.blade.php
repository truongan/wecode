@php($selected = 'notifications')
@extends('layouts.app')
@section('head_title','Edit Notification')
@section('icon', 'fas fa-plus')

@section('title', 'Edit Notification')

@section('body_end')
<script src="{{ asset('assets/ckeditor/ckeditor.js') }}" charset="utf-8"></script>
<script>
$(document).ready(function(){
	CKEDITOR.replace("notif_text");
});
</script>
@endsection

@section('content')
<form action="/notifications/{{ $notification->id }}" method="POST">
    @method('PUT')
    <input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
    <input type="hidden" name="id" value="{{ $notification->id }}"/>

    <p class="input_p">
        <label for="form_title" class="tiny">Title:</label>
        <input id="form_title" name="title" type="text" class="sharif_input" value=" {{$notification->title}} "/>
    </p>
    <p class="input_p">
        <label for="notif_text" class="tiny">Text:</label><br><br>
        <textarea id="notif_text" name="text"> {!!$notification->text!!} </textarea>
    </p>
    <p class="input_p">
        <input type="submit" value="Save" class="sharif_input"/>
    </p>
</form>	
@endsection