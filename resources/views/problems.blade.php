@extends('layouts.app')

@section('icon', 'fas fa-clipboard-list')

@section('title', 'All Problems')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}
    <span class="title_menu_item">
        <a href="{{ url('problems/add') }}"><i class="fas fa-plus fa-lg color8"></i> Add</a>
    </span>
    <span class="title_menu_item">
            <a href="{{ url('submissions/all/assignments/0') }}"><i class="fas fa-list-ul fa-lg color8"></i>Review test submissions for problems</a>
    </span>
@endsection

@section('content')
Nội dung ghi ở đây nè :3
@endsection