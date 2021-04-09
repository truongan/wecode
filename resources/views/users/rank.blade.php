@extends('layouts.app')
@php($selected="settings")
@section('head_title','View User')
@section('icon', 'fas fa-users')

@section('title')
Users - ranking
@endsection

@section('content')

<form class="form-inline">
	<div class="form-group">
		<label for="names">Username lists</label>
		<textarea name="names" id="names" class="form-control" placeholder="Paste username list here" aria-describedby="help_names">{{ Request::get('names') }}</textarea>
		<small id="help_names" class="text-muted">A list of usernames, sperarated by white space to see ranking among them</small>
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
<table class="wecode_table table table-striped table-bordered table-sm">
	<thead class="thead-dark">
		<tr>
			<th>#</th>
			<th>Username</th>
			<th><small>clases</small></th>
			<th>Total submission</th>
			<th>No. accepted (Percentage)</th>
			<th>Problem tried</th>
			<th>Problem solved</th>
		</tr>
	</thead>
	@foreach ($users as $user)
		<tr>
			<td>{{$loop->iteration}}
			<td>
				<a href="{{ route('users.show', ['user' => $user->id]) }}"> {{$user->username}}
				</a>
      		</td>
			<td>
        @foreach ($user->lops as $lop)
            <a href="{{ route('lops.show', $lop->id) }}">{{$lop->name}}</a></br>
        @endforeach
			<td>
				<button class="btn btn-info " disabled> {{ $stats[$user->id]->total }} </button>
			</td>
			<td>
				{{ $stats[$user->id]->total_accept }} ({{@round(fdiv($stats[$user->id]->total_accept, $stats[$user->id]->total) * 100, 2) }} )%
			</td>
			<td> 
				{{ count($stats[$user->id]->problem_wise_stat)}}
			</td>
			<td>
				{{ count($stats[$user->id]->solved_problems)}}
			</td>
			
		</tr>
	@endforeach
</table>
@endsection