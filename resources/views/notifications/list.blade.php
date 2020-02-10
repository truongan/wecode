@php($selected = 'notifications')
@extends('layouts.app')

@section('icon', 'fas fa-bell')

@section('title', 'Notifications')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}
    <span class="title_menu_item"><a href="{{ route('notifications.create') }}"><i class="fa fa-plus color10"></i> New</a></span>
@endsection

@section('content')
Nội dung ghi ở đây nè :3
@endsection