@extends('layouts.app')
@section('head_title','Assignments')
@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('other_assets')
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
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
	<a class="nav-link active" href="{{ route('assignments.index') }}"><i class="far fa-star color1"></i>Assingments setting</a>
	<a class="nav-link" href="{{ route('assignments.score_accepted') }}"><i class="far fa-star color1"></i>Assignments score accepted</a>
	<a class="nav-link" href="{{ route('assignments.score_sum') }}"><i class="far fa-star color1"></i>Assignments score olp</a>
</nav></small>
@endsection
@endif
@section('content')
<div class="row">
	<table class="wecode_table table table-striped table-bordered">
		<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>Select</th>
				<th>Class</th>
				<th>Name</th>
				<th>Submissions</th>
				<th>Coefficient</th>
				<th>Start Time</th>
				<th>Finish Time</th>
				<th>Scoreboard</th>
				<th>PDF</th>
				<th>Status</th>
				@if (!in_array( Auth::user()->role->name, ['student']))
					<th>Action</th>
				@endif
			</tr>
		</thead>
		
		@foreach ($assignments as $assignment)
		<tr data-id="{{$assignment->id}}">
			<td>{{$loop->iteration}} </td>
			<td>
				<span data-toggle="tooltip" title="View an assignment's problem or submission will set it as your default assignment">
					<i  class=" far {{ (isset(Auth::user()->selected_assignment->id) && $assignment->id == Auth::user()->selected_assignment->id) ? 'fa-check-square color6' : 'fa-square' }} fa-2x" data-id="{{ $assignment->id }}"></i>
				</span>
			</td>
			<td>
				@foreach ($assignment->lops as $lop)
					<span class="badge badge-pill badge-secondary"><a href="{{ route('lops.show', $lop->id) }}" class="text-white">{{$lop->name}}</a></span><br>
				@endforeach
			</td>
			<td>
				<a href="{{ route('assignments.show',['assignment'=>$assignment,'problem_id'=>$assignment->problems->first()->id??0]) }}" data-toggle="tooltip" title="Click to view problem(s)">
					<strong>{{ $assignment->name }}</strong>
					<br/>
					({{ $assignment->no_of_problems }} problems)
				</a>
			</td>
			<td>
				@if ( in_array( Auth::user()->role->name, ['student']) )
					<a href="{{ route('submissions.index', [$assignment->id, Auth::user()->id, 'all', 'all'])}}" data-toggle="tooltip" title="View all submissions">
						<small>{{$assignment->total_submits}} submission{{ $assignment->total_submits > 1 ? 's' : ''}}</small>
					</a>
				@else
					<a href="{{ route('submissions.index', [$assignment->id, 'all', 'all', 'all'])}}" data-toggle="tooltip" title="View all submissions">
						<small>{{$assignment->total_submits}} submission{{ $assignment->total_submits > 1 ? 's' : ''}}</small>
					</a>
				@endif
			</td>
			<td>
				@if ($assignment->finished)
					<span style="color: red;">Finished</span>
				@else
					@if($assignment->coefficient != "error")
						{{$assignment->coefficient}}%
					@else
						<span style="color: red;">! Error</span>
					@endif
				@endif
			</td>
			<td>{{$assignment->start_time}}</td>
			<td>{{$assignment->finish_time}}</td>
			<td>
				@if ($assignment->score_board)
					<a href="{{ url("scoreboard/full/$assignment->id")}}" data-toggle="tooltip" title="Click to viewa assignment's scoreboard">View<i class="fas fa-external-link-alt"></i></a>
				@else
					<a href="{{ url("scoreboard/full/$assignment->id")}}"  data-toggle="tooltip" title="Scoreboard closed, admin view only" ><span class="text-secondary">View<i class="fas fa-external-link-alt "></i></span></a>
				@endif
			</td>
			<td>
				<a href="{{ url("assignments/pdf/$assignment->id") }}"><i class="far fa-lg fa-file-pdf"></i></a>
			</td>
			<td>
				@if ($assignment->open)
					<span class="text-success">Open</span>
				@else
					<span class="text-danger">Close</span>
				@endif
			</td>
			@if (!in_array( Auth::user()->role->name, ['student']))
			<td>
				
				<!-- {% if user.level >= 1 %} -->
				<a href="{{ route('assignments.download_submissions', ['type'=>'by_user', 'assignment_id'=>$assignment->id]) }}"><i title="Download Final Submissions (by user)" class="fa fa-download fa-lg color12"></i></a>
				<a href="{{ route('assignments.download_submissions', ['type'=>'by_problem', 'assignment_id'=>$assignment->id]) }}"><i title="Download Final Submissions (by problem)" class="fa fa-download fa-lg color2"></i></a>
				<a href="{{ route('assignments.download_all_submissions', $assignment->id) }}"><i title="Download all submissions" class="fas fa-cloud-download-alt"></i></a>
				<!-- {% endif %}
				{% if user.level >= 2 %} -->
					<a href="#"><i title="Detect Similar Codes" class="fa fa-user-secret fa-lg color7"></i></a>
				<!-- {% endif %}
				{% if user.level >= 2 %} -->
					<a href="{{ route('assignments.reload_scoreboard', $assignment->id) }}"><i title="Force reload scoreboard" class="fa fa-retweet fa-lg color11"></i></a>
				<!-- {% endif %}
				{% if user.level >= 2 %} -->
					<a title="Edit" href="{{ route('assignments.edit', $assignment) }}"><i class="fas fa-edit fa-lg color9"></i></a>
				<!-- {% endif %}
				{% if user.level >= 2 %} -->
					<span title="Delete Assignment" class="del_n delete_Assignment pointer"><i title="Delete Assignment" class="far fa-trash-alt fa-lg color1"></i></span>
				<!-- {% endif %} -->
					</td>
			@endif
		</tr>
		@endforeach
	</table>
</div>

<div class="modal fade" id="assignment_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this assignment?</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger confirm-assignment-delete">YES</button>
      <button type="button" class="btn btn-primary" data-dismiss="modal">NO</button>
        </div>
      </div>
    </div>
</div>
@endsection
@section('body_end')
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function () {
	$('.del_n').click(function () {
	var row = $(this).parents('tr');
		var id = row.data('id');
	$(".confirm-assignment-delete").off();
	console.log("{{ csrf_token() }}");
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

    $("table").DataTable({
		"pageLength": 10,
		"lengthMenu": [ [10, 20, 30, 50, -1], [10, 20, 30, 50, "All"] ]
	});
});
</script>
@endsection