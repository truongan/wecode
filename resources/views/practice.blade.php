@extends('layouts.app')
@php($selected = 'users')
@section('head_title','Practice')
@section('icon', 'fas fa-khanda fa-fw fa-lg')

@section('title', 'Practice')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
@endsection

@section('title_menu')
@endsection

@section('content')
<div class="row">
	<div class="table-responsive">
		<table class="table table-striped table-bordered">
			<thead class="thead-dark">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Lang</th>
					<th><small>Accepted/Tried</small></th>
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
					<span class="badge badge-pill badge-secondary">{{$lang_name->name}}</span>
					@endforeach
				</td>
				<td> <a href="{{ route('submissions.index', ['assignment_id' => 0, 'problem_id' => $problem->id, 'user_id' =>'all', 'choose' => 'all']) }}"> {{$problem->accepted_submission}}/{{$problem->total_submission}}</a></td>
				<td>
					@foreach ($problem->tags as $tag_name)
					<span class="badge badge-pill badge-info">{{$tag_name->text}}</span>
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
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
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