@extends('layouts.app')
@php($selected="settings")
@section('head_title','Edit User')
@section('icon', 'fas fa-users')

@section('title')
Users - {{$user->username}}
@endsection

@section('content')
@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif
<form action="{{ route('users.update', $user)}}" method="POST">
	@csrf
	@method('PATCH')

	<div class="col-6">
		{{-- {% if form_status == 'ok' %}
		<div class="alert alert-success">Profile updated successfully.</div>
		{% elseif form_status == 'error' %}
		  <div class="alert alert-danger">Error updating profile.</div>
		{% endif %} --}}
		{{-- {{ form_open('profile/'~id) }} --}}
		<div class="form-group form-row form-row">
		  <label for="col-2 form_username" class="col-4">Username:	</label>
		  <div class="col-8">
			<input id="form_username" type="text" name="username" class="form-control" value="{{$user->username}}"  disabled/>
			<small class="form-text text-muted">You cannot change username.</small>
		  </div>
		</div>
		<div class="form-group form-row">
		  <label for="form_name" class="col-4">Name:</label>
		  
		  <div class="col-8">
			<input id="form_name" type="text" name="display_name" class="form-control" value="{{$user->display_name}}"/>
			{{-- {{ form_error('display_name', '<div class="alert alert-danger">', '</div>') }} --}}
		  </div>
		</div>
		<div class="form-group form-row">
		  <label for="form_email" class="col-4">Email:</label>
			<div class="col-8">
			<input id="form_email" type="text" name="email" class="form-control" value="{{$user->email}}"/>
			{{-- {{ form_error('email', '<div class="form-text text-danger">', '</div>') }} --}}
		  </div>
		</div>
		<div class="form-group form-row">
		  <label for="form_password" class="col-4">Password:<br>
		  </label>
		  <div class="col-8">
			<input id="form_password" type="password" name="password" class="form-control"/>
			<span class="form-text text-muted">If you don't want to change password, leave this blank.</span>
			{{-- {{ form_error('password', '<span class="form-text text-danger">', '</span>') }} --}}
		  </div>
		</div>
		<div class="form-group form-row">
		  <label for="form_password_2" class="col-4">Password, Again:</label>
		  <div class="col-8">
			<input id="form_password_2" type="password" name="password_again" class="form-control"/>
			{{-- {{ form_error('password_again', '<div class="form-text text-danger">', '</div>') }} --}}
		  </div>
		</div>
		@if ( in_array( Auth::user()->role->name, ['admin']) )
		<div class="form-group form-row">
		  <label for="form_role" class="col-4">User Role:</label>
		  <div class="col-8">
			<select id="form_role" name="role_id" class="form-control custom-select">
			  <option value="1" {{ $user->role_id=='1' ? "selected":"" }} >admin</option>
			  <option value="2" {{ $user->role_id=='2' ? "selected":"" }} >head_instructor</option>
			  <option value="3" {{ $user->role_id=='3' ? "selected":"" }} >instructor</option>
			  <option value="4" {{ $user->role_id=='4' ? "selected":"" }} >student</option>
			</select>
			{{-- {{ form_error('role', '<div class="form-text text-danger">', '</div>') }} --}}
		  </div>
		</div>
		@endif
		<div class="form-group form-row">
		  <input type="submit" value="Save" class="form-control btn btn-primary"/>
		</div>
	</div>
</form>
	
@endsection

