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
			<th><small>Assignments</small></th>
			<th>Classes</th>
			<th>No. of submission</th>
			<th>No. accepted (Percentage)</th>
			<th>Problem solved (scored)</th>
			<th>Testcase score</th>
		</tr>
	</thead>
	@foreach ($ass as $as)
		<tr>
			<td>{{$loop->iteration}}
			<td>
        <a href="{{ route('assignments.show', ['assignment'=> $as->ass->id, 'problem_id'=>0]) }}"> {{$as->ass->name}}</a>
      </td>
			<td>
        @foreach ($as->ass->lops as $lop)
            <a href="{{ route('lops.show', $lop->id) }}">{{$lop->name}}</a></br>
        @endforeach
			<td>
          <button class="btn btn-info " disabled> {{ $as->total }} </button>
        @if ($as->ass->scoreboard)
          <a href="{{ route('scoreboards.index', $as->ass->id) }} "><i class="fas fa-external-link-alt"></i></a>
        @endif
      </td>
			<td>{{$as->accept}} ({{ round($as->accept / $as->total * 100, 2) }}%)</td>
			<td> 
        <a class="btn btn-outline-success" href="{{ route('submissions.index', ['assignment_id'=>$as->ass->id, 'problem_id'=>'all', 'user_id' => $user->id, 'choose'=>'final']) }}"> 
          {{$as->solved}} ({{ $as->ac_score}})
        </a>
      </td>
			<td>
        <a class="btn btn-outline-danger" href="{{ route('submissions.index', ['assignment_id'=>$as->ass->id, 'problem_id'=>'all', 'user_id' =>   $user->id, 'choose'=>'all']) }}"> 
        {{ $as->score }}
        </a>
      </td>
			
		</tr>
	@endforeach
</table>
@endsection