@extends('layouts.app')
@php($selected = 'users')
@section('head_title','Practice')
@section('icon', 'fas fa-khanda fa-fw fa-lg')

@section('title', 'Practice')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='{{ asset('assets/DataTables/datatables.min.css') }}'/>
@endsection

@section('title_menu')
@endsection

@section('content')
<div class="row">
	<div class="table-responsive">
		<table class="table table-striped table-bordered">
			<thead class="thead-old table-dark">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Lang</th>
					<th><small>Accepted/Tried</small></th>
					<th>Assignments</th>
					<th>editorial</th>
					<th>original author</th>
					<th>Tag</th>
				</tr>
			</thead>
			
			@foreach ($problems as $problem)
			<tr data-id="{{$problem->id}}">
				<td>{{$problem->id}}</td>
				<td>
				<a href="{{ route('practices.show', $problem->id)}}">{{$problem->name}}</a>
				</td>
				<td>
					@foreach ($problem->languages as $lang_name)
					<span class="badge rounded-pill bg-secondary">{{$lang_name->name}}</span>
					@endforeach
				</td>
				<td> <a href="{{ route('submissions.index', ['assignment_id' => 0, 'problem_id' => $problem->id, 'user_id' =>'all', 'choose' => 'all']) }}"> {{$problem->accepted_submission}}/{{$problem->total_submission}}</a></td>
				<td>
					<a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#assignment_list_{{$problem->id}}" aria-expanded="false" aria-controls="assignment_list_{{$problem->id}}">
						{{ $problem->assignments->count()}}<small> assignments</small>
					</a>
					<div class="collapse" id="assignment_list_{{$problem->id}}">
						
						@foreach ($problem->assignments as $assignment)
							<a href="{{ route('submissions.index', ['assignment_id' => $assignment->id, 'problem_id' => $problem->id, 'user_id' => 'all' , 'choose' => 'all']) }}" >
							<span class="btn  btn-secondary btn-sm my-1">{{$assignment->name}} <span class="badge bg-info">{{$assignment->user->username ?? "no-owner"}}</span> </span></a>
						@endforeach
					</div>

				</td>
				<td>
					@if ($problem->editorial != null)
						<a href="{{ $problem->editorial }} "><i class="fas fa-lightbulb  fa-2x  "></i> Editorial </a>
					@endif
				</td>
				<td>
					{{$problem->author}}
				</td>
				<td>
					@foreach ($problem->tags as $tag_name)
					<span class="badge rounded-pill bg-info">{{$tag_name->text}}</span>
					@endforeach
				</td>
			</tr>
			@endforeach
		</table>
		<div class=" d-flex justify-content-center">{{$problems->links(null, ['class'=>'justify-content-center'])}}</div>
	</div>
</div>
@endsection

@section('body_end')

<script type="text/javascript" src="{{ asset('assets/DataTables/datatables.min.css') }}"></script>
<script>
$(document).ready(function () {
    $("table").DataTable({
		"paging": false,
		"ordering": false,
	});
	document.querySelector('.dataTables_filter > label').childNodes[0].data = "Filter in this page";
});
</script>
@endsection