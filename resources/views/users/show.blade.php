@extends('layouts.app')
@php($selected="settings")

@section('other_assets')
<link rel="stylesheet" type='text/css' href="{{ asset('assets/frappe/frappe-charts.min.css') }}"/>
<script src="{{ asset('assets/frappe/frappe-charts.min.cjs.js') }}"></script>
@endsection

@section('head_title','View User')
@section('icon', 'fas fa-users')

@section('title')
Users - {{$user->username}}
@endsection

@section('content')
<div class=" form-inline">
  <div class="form-group">
    <label for="form_username">Username:	</label>
    <div class="col-8">
      <input id="form_username" type="text" name="username" class="form-control" value="{{$user->username}}"  disabled/>
    </div>
  </div>
  <div class="form-group">
    <label for="form_name">Name:</label>
    <div class="col-8">
      <input id="form_name" type="text" name="display_name" class="form-control" value="{{$user->display_name}}" disabled/>
    </div>
  </div>
  <div class="form-group">
    <label for="form_email">Email:</label>
      <div class="col-8">
      <input id="form_email" type="text" name="email" class="form-control" value="{{$user->email}}" disabled/>
    </div>
  </div>  
  <div class="form-group">
    <label for="form_role">Role:</label>
    <div class="col-8">
      <input id="form_name" type="text" name="display_name" class="form-control" value="{{$user->role->name}}" disabled/>
    </div>
  </div>
</div>

<table class=" table table-striped table-bordered table-sm">
  <thead class="thead">
    <th>Total submit</th>
    <th>Accepted submit</th>
    <th>Problem tried</th>
    <th>Problem solved</th>
  </thead>
  <tr>
    <td>{{$stat['total_sub']}}</td>
    <td>{{ $stat['total_accept']}}</td>
    <td>{{ count($stat['prob_wise']) }}</td>
    <td>{{ count($stat['solved_problems']) }}</td>
  </tr>
  <thead class="thead">
    <th>Accept percentage</th>
    <th>Solved percentage</th>
    <th>Avg tries to solve</th>
    <th>Ranking</th>
  </thead>
  <tr >
    <td>{{ @round($stat['total_accept'] *100/ $stat['total_sub'],2) . "%" }}</td>
    <td>{{ @round( count($stat['solved_problems'])*100/ count($stat['prob_wise'])) . "%"}}</td>
    <td>{{ @round( array_sum($stat['solved_problems']) / count($stat['prob_wise']),2) }}</td>
    <td></td>
  </tr>
</table>

{{-- Show contribution map bằng cái này : https://github.com/frappe/charts --}}

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