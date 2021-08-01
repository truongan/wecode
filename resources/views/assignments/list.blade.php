@php($selected = 'assignments')
@extends('layouts.app')
@section('head_title','Assignments')
@section('icon', 'fas fa-folder-open')
@section('title', 'Assignments')

@section('other_assets')
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css'/>
<script>
	if(!!window.performance && window.performance.navigation.type === 2)
	{
		window.location.reload();
	}
</script>
@endsection
@if (!in_array( Auth::user()->role->name, ['student']))
@section('title_menu')
<small><nav class="nav nav-pills">
	<a class="nav-link" href="{{ route('assignments.create') }}"><i class="fa fa-plus color8"></i> Add</a>
	<a class="nav-link active" href="{{ route('assignments.index') }}"><i class="far fa-star text-danger"></i>Assingments setting</a>
	<a class="nav-link" href="{{ route('assignments.score_accepted') }}"><i class="far fa-star text-danger"></i>Assignments score accepted</a>
	<a class="nav-link" href="{{ route('assignments.score_sum') }}"><i class="far fa-star text-danger"></i>Assignments score olp</a>
</nav></small>
@endsection
@endif
@section('content')
@if (\Session::has('success'))
	    <div class="alert alert-success alert-dismissible fade show" role="alert">
		  	<strong>Success!</strong> {!! \Session::get('success') !!}.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

		</div>
	@endif
<div class="row">
	<table class="wecode_table table table-striped table-bordered">
		<thead class="thead-old table-dark">
			<tr>
				<th>ID</th>
				{{-- <th>Owner</th> --}}
				<th><small>Select</small></th>
				<th width="15%">Class</th>
				<th width="15%">Name</th>
				<th width="15%"><small>Submit</small></th>
				{{-- <th>Coef</th> --}}
				<th>Start</th>
				<th>Finish</th>
				<th><small>Score-board</small></th>
				@if (!in_array( Auth::user()->role->name, ['student']))
					<th><small>Open</small></th>
					<th>Action</th>
				@endif
			</tr>
		</thead>
		
		@foreach ($assignments as $assignment)
			@if($assignment->id==0)
				@continue
			@endif
		<tr data-id="{{$assignment->id}}">
			<td><span title="Owner's username and assignment id"> {{$assignment->id}}  </span> </td>
			{{-- <td></td> --}}
			<td>
				<span title="View an assignment's problem or submission will set it as your default assignment">
					<i  class=" far {{ (isset(Auth::user()->selected_assignment->id) && $assignment->id == Auth::user()->selected_assignment->id) ? 'fa-check-square color6' : 'fa-square' }} fa-2x" data-id="{{ $assignment->id }}"></i>
				</span>
			</td>
			<td>
				@foreach ($assignment->lops as $lop)
					<a href="{{ route('lops.show', $lop->id) }}" ><span class="badge rounded-pill bg-secondary">{{$lop->name}}</span></a><br>
				@endforeach
			</td>
			<td>
				<a href="{{ route('assignments.show',['assignment'=>$assignment,'problem_id'=>$assignment->problems->first()->id??0]) }}" title="Click to view problem(s)">
					<strong>{{ $assignment->name }}</strong>
					<br/> (by:{{$assignment->user->username ?? "no owner"}})
				</a>
			</td>
			<td>
				<a href="
					@if ( in_array( Auth::user()->role->name, ['student']) )
						 {{ route('submissions.index', [$assignment->id, Auth::user()->id, 'all', 'all'])}}
					@else
						{{ route('submissions.index', [$assignment->id, 'all', 'all', 'all'])}}
					@endif
				" title="View all submissions">
					<small>{{$assignment->total_submits}} sub</small>
				-
				{{ $assignment->no_of_problems }} prob
				</a>
				<br/>
				<small>
				@if ($assignment->finished)
					<span class="text-danger">Finished</span>
				@else
					@if($assignment->eval_coefficient() === "error")
						<span class="text-danger">!Error late rule</span>
					@else
						<span class="text-info">{{$assignment->coefficient}}% if submit now</span>
					@endif
				@endif
				</small>
			</td>
			<td><small>{{$assignment->start_time->setTimezone($settings['timezone'])->locale('en')->isoFormat('llll (UZZ)') }}</small></td>
			<td><small>{{$assignment->finish_time->setTimezone($settings['timezone'])->locale('en')->isoFormat('llll (UZZ)') }}</small></td>
			<td>
				<a href="{{ route('scoreboards.index', $assignment->id)}}" title="Click to viewa assignment's scoreboard">
					@if ($assignment->score_board)
						View<i class="fas fa-external-link-alt"></i>
					@elseif (!in_array( Auth::user()->role->name, ['student']))
						<span class="text-secondary">View<i class="fas fa-external-link-alt "></i></span>
					@endif
				</a>
				<br/>
				{{-- <a href="{{ url("assignments/pdf/$assignment->id") }}"><i class="far fa-lg fa-file-pdf"></i></a> --}}
			</td>
			@if (!in_array( Auth::user()->role->name, ['student']))
			<td>
				<div class="form-check form-switch">
					<input id="ass{{$assignment->id}}" class="form-check-input"  type="checkbox" value="{{$assignment->open}}" {{$assignment->open ? 'checked' : ''}} />
				</div>
			</td>
			<td>
				<a href="{{ route('assignments.duplicate', $assignment->id) }}" title="Duplicate assignment" ><i title="Duplicate assignment" class="far fa-copy fa-lg text-danger"></i></a>
				<a href="{{ route('submissions.rejudge_view', $assignment->id) }}"><i title="Rejudge submissions" class="fa fa-retweet fa-lg text-success"></i></a>
				<a title="Edit" href="{{ route('assignments.edit', $assignment) }}"><i class="fas fa-edit fa-lg color9"></i></a>
				<span title="Delete Assignment" class="del_n delete_Assignment pointer"><i title="Delete Assignment" class="far fa-trash-alt fa-lg text-danger"></i></span>
				
				<a href="#extra_action_{{$assignment->id}}" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="#extra_action_{{$assignment->id}}" ><i class="fas fa-ellipsis-v text-info m-2"></i></i></a>
				<div class="collapse" id="extra_action_{{$assignment->id}}">
					<a href="{{ route('assignments.download_submissions', ['type'=>'by_user', 'assignment_id'=>$assignment->id]) }}"><i title="Download Final Submissions (by user)" class="fa fa-download fa-lg color12"></i></a>
					<a href="{{ route('assignments.download_submissions', ['type'=>'by_problem', 'assignment_id'=>$assignment->id]) }}"><i title="Download Final Submissions (by problem)" class="fa fa-download fa-lg color2"></i></a>
					<a href="{{ route('assignments.download_all_submissions', $assignment->id) }}"><i title="Download all submissions" class="fas fa-cloud-download-alt"></i></a>
					<a href="{{ route('moss.index', $assignment->id) }}"><i title="Detect Similar Codes" class="fa fa-user-secret fa-lg color7"></i></a>
					<a href="{{ route('assignments.reload_scoreboard', $assignment->id) }}"><i title="Force reload scoreboard" class="fa fa-redo fa-lg text-success"></i></a>
				</div>
			</td>
			@endif
		</tr>
		@endforeach
	</table>
	{{-- @php(dd(DB::getQueryLog())) --}}
</div>

	<div class="modal fade" id="assignment_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this assignment?</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger confirm-assignment-delete">YES</button>
		<button type="button" class="btn btn-primary" data-bs-dismiss="modal">NO</button>
			</div>
		</div>
		</div>
	</div>
@endsection



@section('body_end')

<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.10.25/datatables.min.js"></script>
<script>
$(document).ready(function () {
	$('.del_n').click(function () {
	var row = $(this).parents('tr');
		var id = row.data('id');
	$(".confirm-assignment-delete").off();
	$(".confirm-assignment-delete").click(function(){
		$("#assignment_delete").modal("hide");
		$.ajax({
		type: 'DELETE',
		url: '{{ route('assignments.index') }}/'+id,
		data: {
					'_token': "{{ csrf_token() }}",
		},
		error: shj.loading_error,
		success: function (response) {
			if (response.done) {
			row.animate({backgroundColor: '#FF7676'},100, function(){row.remove();});
			$.notify('assignment deleted'	, {position: 'bottom right', className: 'success', autoHideDelay: 5000});
			$("#assignment_delete").modal("hide");
			}
			else
			shj.loading_failed(response.message);
		}
		});
	});
	$("#assignment_delete").modal("show");
	});

	$('input').change(function(){
		var row = $(this).parents('tr');
		var id = row.data('id');
		console.log(id);
		$.ajax({
		type: 'POST',
		url: '{{ route('assignments.check_open') }}',
		data: {
					'_token': "{{ csrf_token() }}",
					'assignment_id': id,
		},
		error: shj.loading_error,
		success: function (response) {
            if (response == "success"){
                $.notify('Change sucessfully saved', {position: 'bottom right', className: 'success', autoHideDelay: 3500});
                $('.save-button').removeClass('btn-info').addClass('btn-secondary');
                }
            },
        error: function(response){
            $.notify('Error while saving', {position: 'bottom right', className: 'error', autoHideDelay: 3500});
        	}
		});
	});

    $("table").DataTable({
		{{-- "pageLength": 60, --}}
		{{-- "ordering":false, --}}
		"order":['0', 'desc'],
		"columns": [
		    null,
		    { "orderable": false }, //select
		    null,
		    null,
		    null,
		    null,//start
		    null,//finish
		    { "orderable": false }, //scoreboard
		    { "orderable": false }, //open
		    { "orderable": false }, //action
		  ],
		"lengthMenu": [ [20, 60, 150, 500, -1], [20, 60, 150, 500, "All"] ]
	});
});
</script>
@endsection