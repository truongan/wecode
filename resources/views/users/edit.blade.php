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

	<div class="row row-cols-auto g-3 ">
		<div class="card col-12">
			<div class="card-header">
				{{ $user->username }}
			</div>
			<div class="card-body row">
				<div class="col-3">
					<h5>Class(es): </h5>
					<p class="">@foreach ($user->lops as $item)
					<span class="badge bg-light">{{$item->name}}</span>
					@endforeach</p>
				</div>
				<div class="col-3">
					<h5>Remaining trial time</h5>
					<p class="">{{ $user->trial_time ? ($user->created_at->addHours($user->trial_time)->diffForHumans()) : "Permanent user" }}</p>
				</div>
				<div class="col-3">
					<p class=""><strong>Created: </strong>{{ $user->created_at ? ($user->created_at->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('lll')) : "N/A" }}</p>
					<p class=""><strong>Fisrt login: </strong>{{ $user->first_login_time ? ($user->first_login_time->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('lll')) : "N/A" }}</p>
				</div>
				<div class="col-3">
					<p class=""><strong>last updated: </strong> {{ $user->updated_at ? ($user->updated_at->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('lll')) : "N/A" }}</p>
					<p class=""><strong>Last login: </strong>{{ $user->last_login_time ? ($user->last_login_time->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('lll')) : "N/A" }}</p>
				</div>
			</div>
		</div>


		<div class="col-6 form-floating g-3">
			<input id="form_name" type="text" name="display_name" class="form-control form-floating" value="{{$user->display_name}}" />
			<label for="form_name" >Name:</label>
			{{-- {{ form_error('display_name', '<div class="alert alert-danger">', '</div>') }} --}}
		</div>

		<div class="col-6 form-floating">
			<input id="form_email" type="text" name="email" class="form-control" value="{{$user->email}}" />
			<label for="form_email" class="col-4">Email:</label>
			{{-- {{ form_error('email', '<div class="form-text text-danger">', '</div>') }} --}}
		</div>
		@if ( in_array( Auth::user()->role->name, ['admin']) )
		<div class="col-6 form-floating">
			<select id="form_role" name="role_id" class="form-select">
				<option value="1" {{ $user->role_id=='1' ? "selected":"" }}>admin</option>
				<option value="2" {{ $user->role_id=='2' ? "selected":"" }}>head_instructor</option>
				<option value="3" {{ $user->role_id=='3' ? "selected":"" }}>instructor</option>
				<option value="4" {{ $user->role_id=='4' ? "selected":"" }}>student</option>
				<option value="4" {{ $user->role_id=='4' ? "selected":"" }}>guest</option>
			</select>
			<label for="form_role" >User Role:</label>
			{{-- {{ form_error('role', '<div class="form-text text-danger">', '</div>') }} --}}
		</div>

		<div class="col-6 form-floating">
			<input type="number" class="form-control" name="trial_time" value="{{ $user->trial_time }}" />
			<label >Trial time</label>
		</div>
		@endif

		<hr class="col-12 ">


		<div class="col-6 form-floating">
			<input id="form_password" type="password" name="password" class="form-control" />
			<label for="form_password" >New Password:<br></label>
			<span class="form-text text-muted">If you don't want to change password, leave this blank.</span>
			{{-- {{ form_error('password', '<span class="form-text text-danger">', '</span>') }} --}}
		</div>
 
		<div class="col-6 form-floating">
			<input id="form_password_2" type="password" name="password_confirmation" class="form-control" />
			<label for="form_password_2" >New Password, Again:</label>
			{{-- {{ form_error('password_again', '<div class="form-text text-danger">', '</div>') }} --}}
		</div>
		<div class="col-6 form-floating">
			<input id="form_password_2" type="password" name="old_password" class="form-control" />
			<small class="form-text text-small text-muted">
				This field would be checked only if you changed your own password. You have to supplied your old password in
				order to change it
			</small>
			<label for="form_password_2" >Old Password:</label>
			{{-- {{ form_error('password_again', '<div class="form-text text-danger">', '</div>') }} --}}
		</div>

		<input type="submit" value="Save" class="col form-control col btn btn-primary" />
	</div>
</form>
	
@endsection

