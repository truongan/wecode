@extends('layouts.app')
@php($selected="settings")

@section('other_assets')

<link rel="stylesheet" type='text/css' href="{{ asset('assets/frappe/frappe-charts.min.css') }}"/>
<script src="{{ asset('assets/frappe/frappe-charts.min.iife.js') }}"></script>
  {{-- <script src="https://cdn.jsdelivr.net/npm/frappe-charts@1.2.4/dist/frappe-charts.min.iife.js"></script> --}}
  {{-- <!-- or -->
  <script src="https://unpkg.com/frappe-charts@1.2.4/dist/frappe-charts.min.i.js"></script> --}}


@endsection


@section('title_menu')

  <a class=" ms-4 fs-6 link-dark" href="{{ route('users.edit', $user) }}"> <i class="fa fa-user-edit color2"></i>Edit user profile</a>
  <a class="ms-2 fs-6 link-dark" href="{{ route('users.index') }}"> <i class="fa fa-list color2"></i>List all users</a>

@endsection

@section('body_end')

<script>
let data = {
    dataPoints: {!! $heat_map_data->pluck('count', 'date')->toJson()  !!},
    {{-- start: {!! $heat_map_data->first()->date !!}, // a JS date object
    end: {!! $heat_map_data->last()->date !!} --}}
};

let chart = new frappe.Chart("#heat_map", {
    title: 'Submit heat map since last year',
    type: 'heatmap',
    data: data,
    radius:4,
    {{-- colors: ['violet'] --}}
});

data2 = {
    labels: {!! $hourly_data->pluck('hour')->toJson() !!},
    datasets: [
        { values:{!!$hourly_data->pluck('count')->toJson() !!} }
    ]
}
new frappe.Chart( "#hourly", {
    data: data2,
    type: 'bar',
    title: 'Submit count for each hours of the day ',
    height: 250,
    colors: ['orange']
});
new frappe.Chart( "#pre_score", {
    data: {
      labels : {!! $pre_score_data->pluck('pre_score')->toJson() !!},
      datasets: [
        {values: {!! $pre_score_data->pluck('count')->toJson() !!}}
      ]
    },
    type: 'bar',
    title: 'Percentage of test cased solved for aggregated for all submissions',
    height: 250,
    colors: ['orange']
});

</script>
@endsection

@section('head_title','View User')
@section('icon', 'fas fa-users')

@section('title')
Users - {{$user->username}} 
@endsection

@section('content')
<div class="row mb-3">
	<div class="col-4 mb-3">
		<div class="input-group ">
			<span class="input-group-text" for="form_username">Username: </span>
			<input id="form_username" type="text" name="username" class="form-control" value="{{$user->username}}"
				disabled />
		</div>
	</div>
	<div class="col-4">
		<div class="input-group ">
			<span class="input-group-text" for="form_name">Name:</span>
			<input id="form_name" type="text" name="display_name" class="form-control" value="{{$user->display_name}}"
				disabled />
		</div>
	</div>
	<div class="col-4">
		<div class="input-group ">
			<span class="input-group-text" for="form_email">Email:</span>
			<input id="form_email" type="text" name="email" class="form-control" value="{{$user->email}}" disabled />
		</div>
	</div>
	<div class="col-4">
		<div class="input-group ">
			<span class="input-group-text" for="form_role">Role:</span>
			<input id="form_name" type="text" name="display_name" class="form-control" value="{{$user->role->name}}"
				disabled />
		</div>
	</div>
	<!-- <div class="col-4">
		<div class="input-group ">
			<span class="input-group-text" for="form_role">Trial left</span>
			<input id="form_name" type="text" name="display_name" class="form-control" value="{{ $user->trial_time ? ($user->created_at->addHours($user->trial_time)->diffForHumans()) : "Permanent user" }}"
				disabled />
		</div>
	</div> -->

	<div class="col-4">
		<div class="input-group ">
			<span class="input-group-text" for="form_Name_school">School Name</span>
			<input id="form_Name_school" type="text" name="Name_school" class="form-control" value="{{$user->Name_school}}"
				disabled />
		</div>
	</div>


	<div class="col-4">
		<div class="input-group ">
			<span class="input-group-text" for="form_role">First login</span>
			<input id="form_name" type="text" name="display_name" class="form-control" value="{{ $user->first_login_time ? $user->first_login_time->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('llll (UZZ)') : 'Never'}}"
				disabled />
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
    <td>{{ @round( fdiv($stat['total_accept'] *100 , $stat['total_sub']),2) . "%" }}</td>
    <td>{{ @round( fdiv(count($stat['solved_problems'])*100 , count($stat['prob_wise'])) ,2 ) . "%"}}</td>
    <td>{{ @round( fdiv(array_sum($stat['solved_problems']) , count($stat['prob_wise']) ),2) }}</td>
    <td>{{ $user->elo ?? ""}}</td>
  </tr>
</table>

{{-- Show contribution map bằng cái này : https://github.com/frappe/charts --}}

<div id="heat_map"></div>
<div class='row'>
  
<div class='col-8' id="hourly"></div>
<div class='col-4' id="pre_score"></div>

</div>


<table class="wecode_table table table-striped table-bordered table-sm">
	<thead class="thead-old table-dark">
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
      </td>
			<td>
        <button class="btn btn-info " disabled> {{ $as->total }} </button>
        {{-- ONE !! ONE fucking typo turn a useless if check into a massive query with zero usefuless --}}
        {{-- @if ($as->ass->score_board) --}}
          <a href="{{ route('scoreboards.index', $as->ass->id) }} "><i class="fas fa-external-link-alt"></i></a>
        {{-- @endif --}}
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