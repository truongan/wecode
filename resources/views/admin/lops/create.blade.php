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
<form action="{{route('lops.store')}}" method="POST">
@csrf
    <div class="form-group">
      <label for="name">Class name</label>
      <input type="text"
        class="form-control" name="name" id="name" aria-describedby="_name_desc" placeholder="name">
      <small id="_name_desc" class="form-text text-muted">The name of this new class</small>
    </div>
    <div class="form-group">
      <label for="">List of users' names</label>
      <textarea
        class="form-control" name"user_list" id="" aria-describedby="helpId" placeholder=""></textarea>
      <small id="helpId" class="form-text text-muted">The list of users names for every one enroll  in the class, separated by comma</small>
    </div>
    <button type="submit" class="btn btn-primary">Add</button>
</form>
@endsection