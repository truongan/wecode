@php($selected = 'settings')
@extends('layouts.app')

@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('title_menu')
	{{-- Nếu là admin thì hiển thị --}}
	<span class="title_menu_item"><a href="{{ url('assignments/add') }}"><i class="fa fa-plus color8"></i> Add</a></span>
@endsection

@section('content')
<div class="card text-left ">
  <a href="{{ route('settings.index') }}">
    <div class="card-body">
      <h4 class="card-title">Settings</h4>
      <p class="card-text">General system settings</p>
    </div>
  </a>
</div>
<div class="card text-left ">
  <a href="{{ route('users.index') }}">
    <div class="card-body">
      <h4 class="card-title">Users</h4>
      <p class="card-text">Manage users</p>
    </div>
  </a>
</div>
<div class="card text-left ">
  <a href="{{ route('users.index') }}">
    <div class="card-body">
      <h4 class="card-title">Classes</h4>
      <p class="card-text">Manage classes and users enrollment</p>
    </div>
  </a>
</div>
<div class="card text-left ">
  <a href="{{ route('users.index') }}">
    <div class="card-body">
      <h4 class="card-title">Problem lists</h4>
      <p class="card-text">Mangae problems</p>
    </div>
  </a>
</div>
<div class="card text-left ">
  <a href="{{ route('users.index') }}">
    <div class="card-body">
      <h4 class="card-title">Class</h4>
      <p class="card-text">Manage classes and users enrollment</p>
    </div>
  </a>
</div>
<div class="card text-left ">
  <a href="{{ route('users.index') }}">
    <div class="card-body">
      <h4 class="card-title">Class</h4>
      <p class="card-text">Manage classes and users enrollment</p>
    </div>
  </a>
</div>
<div class="card text-left ">
  <a href="{{ route('users.index') }}">
    <div class="card-body">
      <h4 class="card-title">Class</h4>
      <p class="card-text">Manage classes and users enrollment</p>
    </div>
  </a>
</div>

@endsection