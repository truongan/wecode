@php($selected = 'problem_list')
@extends('layouts.app')
@section('head_title','New Class')
@section('icon', 'fas fa-school')

@section('title', 'New Class')

@section('title_menu')
	<span class="ms-4 fs-6"><a href="{{ route('lops.index') }}"><i class="fa fa-list text-success"></i>Back to list of class</a></span>
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
<form action="{{route('lops.store')}}" method="POST" class="row g-3">
@csrf
  <div class="col-sm-6 form-floating">
    <input type="text"
      class="form-control" name="name" id="name" aria-describedby="_name_desc" placeholder="name">
    <label for="name">Class name</label>
    <small id="_name_desc" class="form-text text-muted">The name of this new class</small>
  </div>

  <div class="col-sm-6"><div class=" form-check">
    <input type="checkbox" class=" form-check-input" name="open" check="" value="on" id="customCheck1" value="open">
    <label class="custom-control-label" for="customCheck1">Open for enrollment</label>
    <small id="helpId" class="form-text text-muted">User will be able to join any classes that are open for enrollment</small>
  </div></div>
  <div class=" form-floating">
    <textarea style="height: 8em"
      class="form-control" name="user_list"  id="" aria-describedby="helpId" placeholder=""></textarea>
    <label for="">List of users' names</label>
    <small id="helpId" class="form-text text-muted">The list of usernames for every one enroll  in the class, separated by comma, space, tab or</small>
  </div>
  <button type="submit" class="btn btn-primary">Add</button>
</form>
@endsection