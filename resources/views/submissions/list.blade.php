@extends('layouts.app')
@php($selected = 'all_submissions')
{{-- @inject('submission_controller', 'App\Http\Controllers\submission_controller') --}}
@section('other_assets')
	<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
	<link rel='stylesheet' type='text/css' href='{{ asset("assets/prismjs/prism.css") }}'/>
	<style type="text/css">
	.status_button{
		white-space: normal;
	}
	.wcj_log{
		font-size: 0.70em;
	}
	</style>
@endsection
@section('body_end')
	<script type='text/javascript' src="{{ asset('assets/prismjs/prism.js')}}"></script>
	<script type='text/javascript' src="{{ asset('assets/js/submission.js')}}"></script>
	<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
	<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
@endsection
@section('icon')
	fas {{$choose =='all' ? 'fa-bars' : 'fa-map-marker'}}
@endsection
@section('title')
	{{$choose =='all' ? 'All submissions' : 'Final submissions'}}
@endsection
@section('title_menu')
@if ($user_id != 'all' and !in_array( Auth::user()->role->name, ['student'])) 
	<a href="{{route('submissions.index', [$assignment->id, 'all', $problem_id, 'all'])}}">Remove filter user</a>
@endif
@if ($problem_id != 'all')
	<a href="{{route('submissions.index', [$assignment->id, $user_id, 'all', 'all'])}}">Remove filter problem</a>
@endif
@endsection
@section('body_end')
<div class="modal fade" id="submission_modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLongTitle">Modal title</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="text-center">
					<div class="spinner-border" role="status">
						<span class="sr-only">Loading...</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<script type="text/javascript">
		site_url = '{{ url('/') }}';
	</script>
	<script type='text/javascript' src="{{ asset("assets/prismjs/prism.js") }}"></script>
	<script type='text/javascript' src="{{ asset("assets/js/shj_submissions.js") }}"></script>
	<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
	<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>

	<script type="text/javascript">$("nav  > ul.pagination a").addClass("page-link");</script>
@endsection
@section('content')
@if($choose == 'all')
<a href="{{route('submissions.index', [$assignment->id, $user_id, $problem_id, 'all'])}}" class="btn btn-primary active" role="button">All <i class="fas fa-chevron-down"></i></a>
<a style="opacity: 0.3;" href="{{route('submissions.index', [$assignment->id, $user_id, $problem_id, 'final'])}}" class="btn btn-light active" role="button">Final <i class="fas fa-chevron-right"></i></a>
@else
<a style="opacity: 0.3;" href="{{route('submissions.index', [$assignment->id, $user_id, $problem_id, 'all'])}}" class="btn btn-light active" role="button">All <i class="fas fa-chevron-right"></i></a>
<a href="{{route('submissions.index', [$assignment->id, $user_id, $problem_id, 'final'])}}" class="btn btn-primary active" role="button">Final <i class="fas fa-chevron-down"></i></a>
@endif
<hr>
@if ($choose == 'all')
<p><i class="fa fa-warning color3"></i> You cannot change your final submissions after assignment finishes.</p>
@endif
<div class="row">
    <div class="col">
        <div class="table-responsive">
			<table class=" text-center table-responsive table table-bordered {{$choose == 'all' ? 'table-striped' : 'data-table'}}">
				<thead class="thead-dark">
					<tr>
						@if ($choose == 'all')
							<th width="1%" rowspan="1"><small> Final</small></th>
						@endif
						@if ($choose == 'final')
							<th width="1%" rowspan="1">#</th>
						@endif
							<th width="2%" rowspan="1"><small> Submit ID</small></th>
						@if (in_array( Auth::user()->role->name, ['student']))
							<th width="20%"><small> Problem</small></th>
							<th width="10%"><small> Submit Time</small></th>
							<th width="7%"><small> Delay (%)</small></th>
							<th width="1%"><small> Language</small></th>
							<th width="30%"><small> Status</small></th>
							<th width="15%"><small> Code</small></th>
						@else
							<th width="5%"><small> Username</small></th>
							<th width="20%"><small> Name</small></th>
							<th width="20%"><small> Problem</small></th>
							<th width="10%"><small> Submit Time</small></th>
							<th width="1%"><small> Score</small></th>
							<th width="1%"><small> Delay %</small></th>
							<th width="1%"><small> Lang</small></th>
							<th width="6%"><small> Status</small></th>
							<th width="6%"><small> Code</small></th>
							<th width="6%"><small> Log</small></th>
							<th width="1%"><small> Rejudge</small></th>
						@endif
					</tr>
				</thead>
				@foreach ($submissions as $submission)
				<tr data-id="{{$submission->id}}">
					@if ($choose == 'all')
						<td>
							<i class="pointer set_final far {{ $submission->is_final ? 'fa-check-circle color11' : 'fa-circle' }} fa-2x"></i>
						</td>
					@endif
					
					@if ($choose == 'final')
						<td>{{$loop->iteration}} </td>
					@endif
					<td>{{$submission->id}}</td>
					@if (!in_array( Auth::user()->role->name, ['student']))
					<td>
						<a href="{{route('submissions.index', [$assignment->id, strval($submission->user_id), $problem_id, 'all'])}}">
							{{$submission->user->username}}<br><i class="fas fa-filter"></i>
						</a>
					</td>
					<td>{{$submission->user->display_name}}</td>
					@endif
					<td>
						<a href="{{route('assignments.show', ['assignment'=>$assignment,'problem_id'=>$submission->problem_id])}}">
							@if ($assignment->id == 0)
								{{$submission->problem->name}}
							@else
								{{ $all_problems[$submission->problem_id]->pivot->problem_name}}
							@endif
						</a><br>
						<a href="{{route('submissions.index', [$assignment->id, $user_id, strval($submission->problem_id), 'all'])}}"><span class="btn btn-info btn-sm"><i class="fas fa-filter"></i></span></a>
					</td>
					<td>{{$submission->created_at}}</small></td>
					@if (!in_array( Auth::user()->role->name, ['student']))
						<td>{{$submission->score}}</td>
					@endif
					<td>
						<span class="small" {{ $submission->delay > 0 ? 'style="color:red;"' :'' }}>
							@if ($submission->delay <= 0)
								No Delay
							@else
								<span title="HH:MM">{{ time_hhmm($submission->delay) }}</span>
							@endif
							</span><br>
						<small>{{ $submission->coefficient }}%</small>
					</td>
					<td>{{$submission->language->name}}</td>
					<td>
						@if (strtolower($submission->status) == "pending")
							<div class="btn btn-secondary">Pending</div>
						@elseif (strtolower($submission->status) == "score")
							@if ($submission->pre_score == 10000)
								<div class="btn btn-success">{{$submission->final_score}}</div>
							@else
								<div class="btn btn-danger">{{$submission->final_score}}</div>
							@endif
						@else 
							<div class="btn btn-danger">{{$submission->status}}</div>
						@endif
					</td>
					<td>
						<div class="btn btn-warning">Code</div>
					</td>

					@if (!in_array( Auth::user()->role->name, ['student']))
					<td>
						<div class="btn btn-secondary">Log</div>
					</td>
					<td>
					<div class="shj_rejudge pointer"><i class="fa fa-redo fa-lg color10"></i></div>
					</td>
					@endif
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>
@endsection

