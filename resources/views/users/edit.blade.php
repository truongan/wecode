@extends('layouts.app')
@php($selected="settings")
@section('icon', 'fas fa-users')

@section('title')
Users - {{$user->username}}
@endsection

@section('content')
<form action="{{ route('users.update', $user)}}" method="POST">
	@csrf
	@method('PATCH')

	<div class="form-group">
		<label for="username">User name</label>
		<input name="username" class="form-control" value="{{$user->username}}">
		<small class="form-text text-muted">Không chứa khoảng trắng, ký tự đặc biệt</small>
	</div>
	<div class="form-group">
		<label for="email">Email</label>
		<input type="email" class="form-control" value="{{$user->email}}">
	</div>
	<div class="form-group">
		<label for="display_name">Display name</label>
		<input class="form-control" value="{{$user->display_name}}">
	</div>
	<div class="form-group">
		<label for="password">Password</label>
		<input name="password" type="password" class="form-control" placeholder="Password">
		<small class="form-text text-muted">Mật khẩu nên bao gồm chữ cái thường và số, không chứa ký tự đặc biệt, khoảng trắng</small>
	</div>
	<div class="form-group">
		<label for="password-confirm" class="control-label">Confirm Password</label>
		<input id="password-confirm" type="password" class="form-control" placeholder="Password confirmation" name="password_confirmation">
	</div>
	<button type="submit" value="Edit" class="btn btn-primary">Edit</button>
</form>
@endsection