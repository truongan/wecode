@extends('layouts.app')
@php($selected="settings")
@section('head_title','View User')
@section('icon', 'fas fa-users')

@section('title')
Users - {{$user->username}}
@endsection

@section('content')
<div class="col-5">
  <div class="form-group">
    <label for="col-2 form_username" class="col-4">Username:	</label>
    <div class="col-8">
      <input id="form_username" type="text" name="username" class="form-control" value="{{$user->username}}"  disabled/>
    </div>
  </div>
  <div class="form-group">
    <label for="form_name" class="col-4">Name:</label>
    <div class="col-8">
      <input id="form_name" type="text" name="display_name" class="form-control" value="{{$user->display_name}}" disabled/>
    </div>
  </div>
  <div class="form-group">
    <label for="form_email" class="col-4">Email:</label>
      <div class="col-8">
      <input id="form_email" type="text" name="email" class="form-control" value="{{$user->email}}" disabled/>
    </div>
  </div>  
  <div class="form-group">
    <label for="form_role" class="col-4">Role:</label>
    <div class="col-8">
      <input id="form_name" type="text" name="display_name" class="form-control" value="{{$user->role->name}}" disabled/>
    </div>
  </div>
</div>

<table class="wecode_table table table-striped table-bordered table-sm">
	<thead class="thead-dark">
		<tr>
			<th>#</th>
			<th><small>Assignments</small></th>
			<th>Classes</th>
			<th>AC / tries </th>
			<th>Solved  / AC scores / Scores</th>
		</tr>
	</thead>
	@foreach ($ass as $as)
		<tr>
			<td>{{$loop->iteration}}
			<td>{{$as->ass->name}}</td>
			<td>{{$as->ass->lops->pluck('name')->join(', ') }}</td>
			<td>{{$as->accept}} / {{ $as->total }}</td>
			<td>{{$as->solved}} / {{ $as->ac_score}} / {{ $as->score }}</td>
			
		</tr>
	@endforeach
</table>
@endsection