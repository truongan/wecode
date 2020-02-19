@php($selected = 'settings')
@extends('layouts.app')

@section('icon', 'fas fa-tags')

@section('title', 'Tags')

@section('title_menu')
	<span class="title_menu_item"><a href="{{ route('problems.index') }}"><i class="fa fa-list color11"></i>Back to list of problems</a></span>
@endsection
@section('content')


<div class="row">
  	<form action="{{route('problems.update', $problem->id)}}" method="POST">
	@method('PUT')
	@csrf
	<p>problem name</p>
	<input type="text"
		name="name" id="name" value={{$problem->name}}
	>
	<p>admin note</p>
	<input type="text"
		name="admin_note" id="name" value={{$problem->admin_note}}
	>
	<p>diff arg</p>
	<input type="text"
		name="	diff_arg" id="name" value={{$problem->diff_arg}}
	>
	<p>diff cmd</p>
	<input type="text"
		name="diff_cmd" id="name" value={{$problem->diff_cmd}}
	>
	
	@foreach ($languages as $item)
		<p> edit language {{$item->name}} </p>
		<input type="text" name="enable[]">
		<input type="hidden" name = "language_update[]" value={{$item->id}}>
		
		<input type="text"
			name="time_limit[]" id="name" value={{$item->default_time_limit}}
		>
		<input type="text"
			name="memory_limit[]" id="name" value={{$item->default_memory_limit}}
		>
	@endforeach

	<button type="submit" class="btn btn-primary">Edit</button>
@endsection
