@php($selected = 'settings')
@extends('layouts.app')
@section('head_title','Edit Tags')
@section('icon', 'fas fa-tags')

@section('title', 'Edit Tags')

@section('title_menu')
	<span class="title_menu_item"><a href="{{ route('tags.index') }}"><i class="fa fa-list color11"></i>Back to list of class</a></span>
@endsection

@section('content')


<div class="row">
  	<form action="{{route('tags.update', $tag->id)}}" method="POST">
	@method('PUT')
	@csrf
	<fieldset 
  		@if (Route::currentRouteName() == 'tags.show')
	  		disabled
		@endif
	>
		<div class="form-group">
		<label for="name">Tag name</label>
		<input type="text"
			class="form-control" name="text" id="name" aria-describedby="_name_desc" placeholder="text" value="{{$tag->text}}">
		<small id="_name_desc" class="form-text text-muted">The name of this new class</small>
		</div>
		
		<label>Select problems you wish to remove this tag from</label>
		<div class="table-responsive">
		<table class="table table-striped table-bordered">
			<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>Problem ID</th>
				<th>Problem name</th>
				<th>Admin note</th>
				<th>Select for removal</th>
			</tr>
			</thead>
			@foreach ($tag->problems as $problem)
			<tr data-id="{{$problem->id}}">
				<td> {{$loop->iteration}} </td>
				<td> {{$problem->id}} </td>
				<td id="un"> {{$problem->name}} </td>
				<td>{{$problem->admin_note}}</td>
				<td>
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="remove{{$problem->id}}" name='remove[]' value="{{$problem->id}}">
					<label class="custom-control-label" for="remove{{$problem->id}}">remove</label>
				</div>
				</td>
			</tr>
			@endforeach
		</table>
		@if (Route::currentRouteName() == 'tags.edit')
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