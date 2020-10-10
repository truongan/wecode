@php($selected = 'problem_list')
@extends('layouts.app')
@section('head_title','New Class')
@section('icon', 'fas fa-school')

@section('title', 'New Class')

@section('title_menu')
	<span class="title_menu_item"><a href="{{ route('lops.index') }}"><i class="fa fa-list color11"></i>Back to list of class</a></span>
@endsection

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
    <div class="custom-control custom-checkbox">
      <input type="checkbox" class="custom-control-input" name="open" check="" value="on" id="customCheck1" value="open">
      <label class="custom-control-label" for="customCheck1">Open for enrollment</label>
      <small id="helpId" class="form-text text-muted">User will be able to join any classes that are open for enrollment</small>
    </div>
    <div class="form-group">
      <label for="">List of users' names</label>
      <textarea
        class="form-control" name="user_list" id="" aria-describedby="helpId" placeholder=""></textarea>
      <small id="helpId" class="form-text text-muted">The list of users names for every one enroll  in the class, separated by comma</small>
    </div>
    <button type="submit" class="btn btn-primary">Add</button>
</form>
@endsection