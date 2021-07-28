@php($selected = 'problem_list')
@php($ed = Route::currentRouteName() == 'lops.show' ? '0' : '1')
@extends('layouts.app')
@section('head_title')
	@if ($ed)
		Edit Classes
	@else
		Classes list
	@endif
@endsection
@section('icon', 'fas fa-school')

@section('title')
	@if ($ed)
		Edit Classes
	@else
		Classes list
	@endif
@endsection

@section('title_menu')
	<span class="title_menu_item"><a href="{{ route('lops.index') }}"><i class="fa fa-list color11"></i>Back to list of class</a></span>
	@if (in_array( Auth::user()->role->name, ['admin', 'head_instructor']))
	<span class="title_menu_item"><a href="{{ route('lops.create') }}"><i class="fa fa-plus color11"></i>Add class</a></span>
	@endif
@endsection

@section('content')


	@if ($ed == 0)
		@if ($lop->users()->find(Auth::user()->id) )
			<form  action="{{route('lops.enrol',['lop' => $lop->id, 'in' => 0])}}" method="POST" onsubmit="return confirm('You are going to LEAVE THE CLASS. Some classes may not allow you to re-enrol, are you certain?');">
				@csrf
				<button type="submit" name="unenroll" id="unenroll" class="btn btn-danger" btn-lg btn-block" >Un enrol</button>
			</form>
		@elseif ($lop->open == 1)
			<form  action="{{route('lops.enrol', ['lop' => $lop->id, 'in' => 1])}}" method="POST">
				@csrf
				<button type="submit" name="unenroll" id="unenroll" class="btn btn-primary" btn-lg btn-block">Enrol</button>
			</form>

		@endif 
	@endif

  	<form  action="{{route('lops.update', $lop->id)}}" method="POST">
	@method('PUT')
	@csrf
	<fieldset>
		<div class="form-group">
		<label for="name">Class name</label>
		<input {{$ed == 0 ? 'disabled' : ''}} type="text"
			class="form-control" name="name" id="name" aria-describedby="_name_desc" placeholder="name" value="{{$lop->name}}">
		<small id="_name_desc" class="form-text text-muted" {{$ed == 0 ? 'hidden' : ''}}>The name of this new class</small>
		</div>
		<div class="custom-control custom-checkbox" >
		<input {{$ed == 0 ? 'disabled' : ''}} type="checkbox" class="custom-control-input" name="open"  id="customCheck1" {{$lop->open ? "checked" : "" }}>
		<label class="custom-control-label" for="customCheck1">Open for enrollment</label>
		<small id="helpId" class="form-text text-muted" {{$ed == 0 ? 'hidden' : ''}} >User will be able to join any classes that are open for enrollment</small>
		</div>
		

		<label {{$ed == 0 ? 'hidden' : ''}}>Select users you wish to remove from the class</label>
		<div class="table-responsive">
		<table class="table table-striped table-bordered">
			<thead class="thead-old table-dark">
			<tr>
				<th>#</th>
				<th>User ID</th>
				<th>Username</th>
				<th>Display Name</th>
				<th>Email</th>
				@if ($ed)
					<th>Select for removal</th>
				@endif
			</tr>
			</thead>
			@foreach ($lop->users as $user)
			<tr data-id="{{$user->id}}">
				<td> {{$loop->iteration}} </td>
				<td> {{$user->id}} </td>
				<td id="un"> {{$user->username}} </td>
				<td>{{$user->display_name}}</td>
				<td>{{$user->email}}</td>
				@if ($ed)
				<td>
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="remove{{$user->id}}" name='remove[]' value="{{$user->id}}">
					<label class="custom-control-label" for="remove{{$user->id}}">remove</label>
				</div>
				</td>
				@endif
			</tr>
			@endforeach
		</table>
		@if ($ed)
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