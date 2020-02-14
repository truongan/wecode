@php($selected = 'settings')
@extends('layouts.app')

@section('icon', 'fas fa-school')

@section('title', 'lops')

@section('title_menu')
	<span class="title_menu_item"><a href="{{ route('lops.index') }}"><i class="fa fa-list color11"></i>Back to list of class</a></span>
	<span class="title_menu_item"><a href="{{ route('lops.create') }}"><i class="fa fa-plus color11"></i>Add class</a></span>
@endsection

@section('content')


<div class="row">
  	<form action="{{route('lops.update', $lop->id)}}" method="POST">
	@method('PUT')
	@csrf
	<fieldset 
  		@if (Route::currentRouteName() == 'lops.show')
	  		disabled
		@endif
	>
		<div class="form-group">
		<label for="name">Class name</label>
		<input type="text"
			class="form-control" name="name" id="name" aria-describedby="_name_desc" placeholder="name" value={{$lop->name}}>
		<small id="_name_desc" class="form-text text-muted">The name of this new class</small>
		</div>
		<div class="custom-control custom-checkbox">
		<input type="checkbox" class="custom-control-input" name="open"  id="customCheck1" value="{{$lop->open ? "check" : "" }}" value="open">
		<label class="custom-control-label" for="customCheck1">Open for enrollment</label>
		<small id="helpId" class="form-text text-muted">User will be able to join any classes that are open for enrollment</small>
		</div>
		

		<label>Select users you wish to remove from the class</label>
		<div class="table-responsive">
		<table class="table table-striped table-bordered">
			<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>User ID</th>
				<th>Username</th>
				<th>Display Name</th>
				<th>Email</th>
				<th>Select for removal</th>
			</tr>
			</thead>
			@foreach ($lop->users as $user)
			<tr data-id="{{$user->id}}">
				<td> {{$loop->iteration}} </td>
				<td> {{$user->id}} </td>
				<td id="un"> {{$user->username}} </td>
				<td>{{$user->display_name}}</td>
				<td>{{$user->email}}</td>
				<td>
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="remove{{$user->id}}" name='remove[]' value="{{$user->id}}">
					<label class="custom-control-label" for="remove{{$user->id}}">remove</label>
				</div>
				</td>
			</tr>
			@endforeach
		</table>
		@if (Route::currentRouteName() == 'lops.edit')
			<div class="form-group">
			<label for="">Enroll more users</label>
			<textarea
				class="form-control" name="user_list" id="" aria-describedby="helpId" placeholder=""></textarea>
			<small id="helpId" class="form-text text-muted">List of new users to enrol into the classes</small>
			</div>
			<button type="submit" class="btn btn-primary">Edit</button>
		@endif

	</fieldset>
	</form>
</div>

@endsection


@section('body_end')

<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script>

$(document).ready(function(){
  $("table").DataTable({
		"pageLength": 50,
		"lengthMenu": [ [20, 50, 100, 200, -1], [20, 50, 100, 200, "All"] ]
	});
});

</script>
@endsection