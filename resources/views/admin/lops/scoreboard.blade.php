@php($selected = 'problem_list')
@php($ed = Route::currentRouteName() == 'lops.show' ? '0' : '1')
@extends('layouts.app')
@section('head_title')
	@if ($ed)
		Edit Classes
	@else
		Classes list
	@endif
@endsection
@section('icon', 'fas fa-school')

@section('title')
	@if ($ed)
		Edit Classes
	@else
		Classes list
	@endif
@endsection

@section('title_menu')
	<span class="title_menu_item"><a href="{{ route('lops.index') }}"><i class="fa fa-list color11"></i>Back to list of class</a></span>
	@if (in_array( Auth::user()->role->name, ['admin', 'head_instructor']))
	<span class="title_menu_item"><a href="{{ route('lops.create') }}"><i class="fa fa-plus color11"></i>Add class</a></span>
	@endif
@endsection

@section('content')
    <div class="table-responsive">
		<table class="table table-striped table-bordered">
			<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>User ID</th>
				<th>Username</th>
				<th>Display Name</th>
				@foreach ($lop->assignments as $ass)
					<th>
						{{$ass->name}}
					</th>
				@endforeach
				@foreach ($lop->assignments as $ass)
					<th>
						{{$ass->name}}
					</th>
				@endforeach
			</tr>
			</thead>
			@foreach ($lop->users as $user)
			<tr data-id="{{$user->id}}">
				<td> {{$loop->iteration}} </td>
				<td> {{$user->id}} </td>
				<td id="un"> {{$user->username}} </td>
				<td>{{$user->display_name}}</td>
				@foreach ($lop->assignments as $ass)
					<td class="bg-success">
						<span>{{$user_table[$user->id][$ass->id]['accept_score'] ?? "" }}</span>
					</td>
				@endforeach
				@foreach ($lop->assignments as $ass)
					<td class="bg-danger">
						<span>{{$user_table[$user->id][$ass->id]['score'] ?? ""}}</span>
					</td>
				@endforeach

			</tr>
			@endforeach
		</table>
    </div>
@endsection


@section('body_end')

{{-- <script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script>

$(document).ready(function(){
  $("table").DataTable({
		"pageLength": 50,
		"lengthMenu": [ [20, 50, 100, 200, -1], [20, 50, 100, 200, "All"] ]
	});
});

</script> --}}
@endsection