@extends('layouts.app')
@php($selected = 'all_submissions')
@section('head_title')
	{{$choose =='all' ? 'All submissions' : 'Final submissions'}}
@endsection
{{-- @inject('submission_controller', 'App\Http\Controllers\submission_controller') --}}
@section('other_assets')
	<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css'/>
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
@section('icon')
	fas {{$choose =='all' ? 'fa-bars' : 'fa-map-marker'}}
@endsection
@section('title')
	{{$choose =='all' ? 'All submissions' : 'Final submissions'}}  for <a class="link-dark" href=" @if($assignment->id !=0) {{ route('assignments.edit', $assignment)  }} @else # @endif "> {{$assignment->name}} </a>
@endsection
@section('title_menu')
@if ($user_id != 'all' and !in_array( Auth::user()->role->name, ['student', 'guest'])) 
		<a class="ms-4 fs-6 link-dark" href="{{route('submissions.index', [$assignment->id, 'all', $problem_id, 'all'])}}">Remove filter user</a>
@endif
@if ($problem_id != 'all')
	<a class="ms-4 fs-6 link-dark" href="{{route('submissions.index', [$assignment->id, $user_id, 'all', 'all'])}}">Remove filter problem</a>
@endif
@endsection
@section('body_end')
<div class="modal fade" id="submission_modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLongTitle">Modal title</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

	<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.10.25/datatables.min.js"></script>

	<script type="text/javascript">$("nav  > ul.pagination a").addClass("page-link");</script>
@endsection
@section('content')
<div class="row row-cols-auto mb-2">
	<div class="col">
		@if($choose == 'all')
		<a href="{{route('submissions.index', [$assignment->id, $user_id, $problem_id, 'all'])}}" class="btn btn-primary active" role="button">All <i class="fas fa-chevron-down"></i></a>
		<a style="opacity: 0.3;" href="{{route('submissions.index', [$assignment->id, $user_id, $problem_id, 'final'])}}" class="btn btn-light active" role="button">Final <i class="fas fa-chevron-right"></i></a>
		@else
		<a style="opacity: 0.3;" href="{{route('submissions.index', [$assignment->id, $user_id, $problem_id, 'all'])}}" class="btn btn-light active" role="button">All <i class="fas fa-chevron-right"></i></a>
		<a href="{{route('submissions.index', [$assignment->id, $user_id, $problem_id, 'final'])}}" class="btn btn-primary active" role="button">Final <i class="fas fa-chevron-down"></i></a>
		@endif
		{{-- <hr> --}}
		@if ($choose == 'all')
		<span>
		<i class="fa fa-warning color3"></i> You cannot change your final submissions after assignment finishes.<i class="fa-solid fa-memory"></i><i class="fa-regular fa-clock"></i>
		</span>
		@endif
	</div>

</div>
<div class="row ">
	<div class="table-responsive">
		<table class=" text-center table-responsive table table-bordered {{$choose == 'all' ? 'table-striped' : 'data-table'}}">
			<thead class="thead-old table-dark">
				<tr>
					@if ($choose == 'all')
						<th width="1%" rowspan="1"><small> Final</small></th>
					@endif
					@if ($choose == 'final')
						<th width="1%" rowspan="1">#</th>
					@endif
						<th width="2%" rowspan="1"><small> Submit ID</small></th>
					
					@if (in_array( Auth::user()->role->name, ['student', 'guest']))
	
					@else
						<th width="10%"><small> Username</small></th>
						<th width="6%"><small> Log / rejudge</small></th>
					@endif
					<th width="15%"><small> Problem</small></th>
					<th width="10%"><small> Submit Time</small></th>
					<th width="25%"><small> judge verdict </small></th>
					<th width="1%"><small> Max <i class="far fa-clock"></i>(s)</small></th>
					<th width="1%"><small> Max <i class="fas fa-memory"></i>(kiB) </small></th>
					<th width="6%"><small> Score</small></th>
					<th width="5%"><small> Code</small></th>
				</tr>
			</thead>
			@foreach ($submissions as $submission)
			<tr data-u="{{$submission->user->username}}" data-a="{{ $assignment->id }}" data-p="{{ $submission->problem_id }}" data-id="{{$submission->id}}">
				@if ($choose == 'all')
					<td>
						<i class="pointer set_final far {{ $submission->is_final ? 'fa-check-circle text-success' : 'fa-circle' }} fa-2x"></i>
					</td>
				@endif
				
				@if ($choose == 'final')
					<td>{{$loop->iteration}} </td>
				@endif
				<td>{{$submission->id}}</td>
				@if (!in_array( Auth::user()->role->name, ['student', 'guest']))
				<td>
					<a href="{{route('submissions.index', [$assignment->id, strval($submission->user_id), $problem_id, 'all'])}}">
						{{$submission->user->username}}
					({{$submission->user->display_name}})
					<br/><i class="fas fa-filter"></i>
					</a><br/>
				</td>
				<td>
					<div class="btn btn-secondary" data-type="log">Log</div>
					<span class="shj_rejudge pointer m-2"><i class="fa fa-redo fa-lg color10"></i></span>
				</td>
				@endif
				<td>
					@if ($assignment->id == 0)
						<a href="{{ route('practices.show', $submission->problem_id) }}">
						{{$submission->problem->name}}
					@else
						<a href="{{route('assignments.show', ['assignment'=>$assignment,'problem_id'=>$submission->problem_id])}}">
						{{ $all_problems[$submission->problem_id]->pivot->problem_name ?? "--- removed ---"}}
					@endif
					</a><br>
					<a href="{{route('submissions.create', [$assignment->id,$submission->problem_id,$submission->id])}}"><span class="btn btn-dark btn-sm"><i class="fas fa-edit"></i></span></a>
					<a href="{{route('submissions.index', [$assignment->id, $user_id, strval($submission->problem_id), 'all'])}}"><span class="btn btn-info btn-sm m-1"><i class="fas fa-filter"></i></span></a>
				</td>
				<td title="reward / penalty on submission time: {{ $submission->coefficient }}%">
					<span class="small">
						{{$submission->created_at->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('llll:ss') }}
					</span>
					<br/>
					<span class="small {{ $submission->delay->total('seconds') > 0 ? 'text-danger' :'text-secondary' }} " tool >
						@if ($submission->delay->total('seconds') > 0)
							{{ $submission->delay->forHumans(['short'=>true, 'parts' => 2]) }} late
						@endif
					</span><br>
					
				</td>
				<td class="js-verdict">
					<x-submission.verdict :submission=$submission/>
				</td>
				<td class="js-time">
					@if ((count($submission->judgement->mems ?? []) > 0)) {{max($submission->judgement->times) }}
					@endif
				</td>
				<td class = "js-mem">
					@if ((count($submission->judgement->mems ?? []) > 0))
					{{max($submission->judgement->mems) }}
					@endif
				</td>
				<td class="status js-score">
					{{-- @if (strtolower($submission->status) == "pending")
						<div class="btn btn-secondary pending" data-type="result">PENDING</div>
					@else 
						@if ($submission->pre_score == 10000)
							<div class="btn btn-success" data-type="result">{{$submission->final_score}}</div>
						@else
							<div class="btn btn-danger" data-type="result">{{$submission->final_score}}</div>
						@endif
					@endif --}}
					@if (strtolower($submission->status) != "pending")

						<span class= "lead 
						@if ($submission->pre_score == 10000)
							text-success
						@else
							text-danger
						@endif
						">
							{{$submission->final_score}}
						</span>
					@endif
				</td>
				<td>
					<div class="btn btn-warning" data-type="code">{{$submission->language->name}}</div>
				</td>

			</tr>
			@endforeach
		</table>
	
		<div class=" d-flex justify-content-center">{{$submissions->links(null, ['class'=>'justify-content-center'])}}</div>
	</div>
</div>
@endsection

