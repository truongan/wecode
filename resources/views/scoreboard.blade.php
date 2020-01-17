@extends('layouts.app')

@section('icon', 'fas fa-star')

@section('title', 'Scoreboard')

@section('title_menu')
{{-- thêm assignment.id vào --}}
<span class="title_menu_item">
	<a href="{{ url('scoreboard/full/assignment.id') }}">
	<i class="fas fa-star color10"></i> Full information </a>
</span>
<span class="title_menu_item">
	<a href="{{ url('scoreboard/simplify/assignment.id') }}">
	<i class="fas fa-star-half-alt color10"></i> Minimal information </a>
</span>
@endsection

@section('content')
Nội dung ghi ở đây nè :3
@endsection