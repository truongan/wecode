@extends('layouts.app')
@php($selected="settings")
@section('head_title','Edit User')
@section('icon', 'fas fa-users')

@section('title')
Users - {{$user->username}}
@endsection

@section('title_menu')

  <a class=" ms-4 fs-6 link-dark" href="{{ route('users.show', $user) }}"> <i class="fa fa-user-edit color2"></i>View user profile</a>
  <a class="ms-2 fs-6 link-dark" href="{{ route('users.index') }}"> <i class="fa fa-list color2"></i>List all users</a>

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

	<div class="row offset-lg-2 col-lg-8 g-3">
		{{-- {% if form_status == 'ok' %}
		<div class="alert alert-success">Profile updated successfully.</div>
		{% elseif form_status == 'error' %}
		  <div class="alert alert-danger">Error updating profile.</div>
		{% endif %} --}}
		{{-- <div class="form-group form-old-row row form-old-row row"> --}}
		<label for="col-2 form_username" class="col-4">Username:	</label>
		<div class="col-8">
		<input id="form_username" type="text" name="username" class="form-control" value="{{$user->username}}"  disabled/>
		<small class="form-text text-muted">You cannot change username.</small>
		</div>
		{{-- </div> --}}

		<label for="col-2 form_username" class="col-4">Class:	</label>
		<div class="col-8" disabled>
			@foreach ($user->lops as $item)
				<span class="badge bg-light">{{$item->name}}</span>;
			@endforeach
		</div>

		<label for="form_name" class="col-4">Name:</label>
		
		<div class="col-8">
		<input id="form_name" type="text" name="display_name" class="form-control" value="{{$user->display_name}}"/>
		{{-- {{ form_error('display_name', '<div class="alert alert-danger">', '</div>') }} --}}
		</div>
		<label for="form_email" class="col-4">Email:</label>
		<div class="col-8">
		<input id="form_email" type="text" name="email" class="form-control" value="{{$user->email}}"/>
		{{-- {{ form_error('email', '<div class="form-text text-danger">', '</div>') }} --}}
		</div>
		<label for="form_password_2" class="col-4">Old Password:</label>
		<div class="col-8">
			<input id="form_password_2" type="password" name="old_password" class="form-control"/>
			<small class="form-text text-small text-muted">
			This field would be checked only if you changed your own password. You have to supplied your old password in order to change it
			</small>
			{{-- {{ form_error('password_again', '<div class="form-text text-danger">', '</div>') }} --}}
		</div>
		<label for="form_password" class="col-4">New Password:<br>
		</label>
		<div class="col-8">
		<input id="form_password" type="password" name="password" class="form-control"/>
		<span class="form-text text-muted">If you don't want to change password, leave this blank.</span>
		{{-- {{ form_error('password', '<span class="form-text text-danger">', '</span>') }} --}}
		</div>
		<label for="form_password_2" class="col-4">New Password, Again:</label>
		<div class="col-8">
		<input id="form_password_2" type="password" name="password_confirmation" class="form-control"/>
		{{-- {{ form_error('password_again', '<div class="form-text text-danger">', '</div>') }} --}}
		</div>
		
		@if ( in_array( Auth::user()->role->name, ['admin']) )
		  <label for="form_role" class="col-4">User Role:</label>
		  <div class="col-8">
			<select id="form_role" name="role_id" class="form-control custom-select">
			  <option value="1" {{ $user->role_id=='1' ? "selected":"" }} >admin</option>
			  <option value="2" {{ $user->role_id=='2' ? "selected":"" }} >head_instructor</option>
			  <option value="3" {{ $user->role_id=='3' ? "selected":"" }} >instructor</option>
			  <option value="4" {{ $user->role_id=='4' ? "selected":"" }} >student</option>
			  <option value="4" {{ $user->role_id=='4' ? "selected":"" }} >guest</option>
			</select>
			{{-- {{ form_error('role', '<div class="form-text text-danger">', '</div>') }} --}}
		  </div>

			<label class="col-4">Trial time</label>
		  	<div class="col-8">
			<input type="number" class="form-control" name="trial_time" value="{{ $user->trial_time }}"/>
			</div>
		@endif
		<input type="submit" value="Save" class="form-control col btn btn-primary"/>
	</div>
</form>
	
@endsection

