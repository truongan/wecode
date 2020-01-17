@extends('layouts.app')

@section('icon', 'fas fa-users')

@section('title', 'Users')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}
    <span class="title_menu_item"><a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/users.md" target="_blank"><i class="fa fa-question-circle color6"></i> Help</a></span>
	<span class="title_menu_item"><a href="{{ url('users/add') }}"><i class="fa fa-user-plus color11"></i> Add Users</a></span>
	<span class="title_menu_item"><a href="{{ url('users/list_excel') }}"><i class="fa fa-download color9"></i> Excel</a></span>
@endsection

@section('content')
Nội dung ghi ở đây nè :3
@endsection