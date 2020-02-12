@extends('layouts.app')
@php($selected="settings")
@section('icon', 'fas fa-users')

@section('title')
Users - {{$user->username}}
@endsection

@section('content')
Id
Username
Display name
Email
Role
<br>
{{$user->id}}
{{$user->username}}
{{$user->display_name}}
{{$user->email}}
{{$user->role_id}}
@endsection