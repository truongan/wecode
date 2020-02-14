@extends('layouts.app')
@php($selected="settings")
@section('icon', 'fas fa-clipboard-list')

@section('title','Problems List')

@section('title_menu')
<span class="title_menu_item"><a href="{{ url('problems/create') }}"><i class="fas fa-plus fa-lg color8"></i> Add</a></span>
<span class="title_menu_item"><a href="{{ url('submissions/all/assignments/0') }}"><i class="fas fa-list-ul fa-lg color8"></i>Review test submissions for problems</a></span>
<span class="title_menu_item"><a href="{{ url('problems/download_all') }}"><i class="fas fa-download fa-lg color8"></i>Download all problem's test and description</a></span>
@endsection

@section('content')
@foreach ($problems as $item)
    {{$item}}
@endforeach
@endsection