@php($selected = 'settings')
@extends('layouts.app')

@section('icon', 'fas fa-plus')

@section('title', 'New Language')

@section('content')
<form method="POST"  action="{!! route('languages.store') !!}">
	<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>

	<table class="table w-50">
		<tr>
					<td>Name:</td>
					<td>	<input class="form-control" type="text" name="name">	</td>
		</tr>
		<tr>
					<td>Extension:</td>
					<td>	<input class="form-control" type="text" name="extension">	</td>
		</tr>
		<tr>
					<td>Sorting:</td>
					<td>	<input class="form-control" type="text" name="sorting">	</td>
		</tr>
		<tr>
					<td>Default_time_limit:</td>
					<td>	<input class="form-control" type="text" name="default_time_limit">	</td>
		</tr>
		<tr>
					<td>Default_memory_limit:</td>
					<td>	<input class="form-control" type="text" name="default_memory_limit">	</td>
		</tr>
	
		
	</table>
	<br>
	<div class="d-flex justify-content-center w-50">
		<input type="submit" value="Add" class="form-control btn btn-primary"/>
	</div>
</form>	
@endsection