@extends('layouts.app')
@php($selected="settings")
@section('icon', 'fas fa-bell')

@section('title','Notification')

@section('title_menu')
@endsection

@section('content')
<div id="number{{ $notification->id }}" data-id="{{ $notification->id }}"> 
	<div class="notif_title">
		<h2>{{ $notification->title }}</h2>
		<div class="notif_meta">
			{{ $notification->created_at }}
			@if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
				<a href="{{ $notification->id }}/edit">Edit</a>
			@endif
        </div>
        <hr>
    </div>
    <div class="notif_text">
        {!! $notification->text !!}
    </div>
</div>

@endsection