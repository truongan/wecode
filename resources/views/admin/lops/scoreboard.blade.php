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

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
@endsection

@section('content')
<div class="row">
    <div class="table-responsive">
		<table class="table table-striped table-bordered">
			<thead class="thead-dark">
			<tr>
				<th>#</th>
				{{-- <th>User ID</th> --}}
				<th>User</th>
				<th><small>Name</small></th>
				<th><small>Sum ac</small></th>
				@foreach ($lop->assignments as $ass)
					<th><small>
						<a href="{{ route('assignments.show', ['assignment'=> $ass->id, 'problem_id' => '0'] ) }}" >
						{{$ass->name}}
						</a>
					</small></th>
				@endforeach
				<th><small>Sum</small></th>
				@foreach ($lop->assignments as $ass)
					<th><small>
						{{$ass->name}}
					</small></th>
				@endforeach
			</tr>
			</thead>
			@foreach ($lop->users as $user)
			<tr data-id="{{$user->id}}">
				<td id="un">{{$loop->iteration}}</td>
				<td id="un">{{$user->username}}</td>
				<td> <small>{{$user->display_name}} </small></td>
				<td> <p class="lead text-success">{{$user_table[$user->id]['sum_ac'] ?? 0}} </p></td>
				@foreach ($lop->assignments as $ass)
					<td class="">
						@if (isset($user_table[$user->id][$ass->id]['accept_score']))
							
						<a class="btn  btn-outline-success" href="{{ route('submissions.index', ['assignment_id' => $ass->id, 'problem_id' => 'all', 'user_id' => $user->id, 'choose' => 'final']) }}">
						{{$user_table[$user->id][$ass->id]['accept_score'] }}
						</a>
						@endif
					</td>
				@endforeach
				<td> <p class="lead text-danger">{{$user_table[$user->id]['sum'] ?? 0}} </p></td>
				@foreach ($lop->assignments as $ass)
					<td class="bg-danger">
						<span>{{$user_table[$user->id][$ass->id]['score'] ?? ""}}</span>
					</td>
				@endforeach

			</tr>
			@endforeach
		</table>
    </div>
</div>
@endsection


@section('body_end')
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>

<script>

$(document).ready(function(){
   	var t =  $("table").DataTable({
	  	"paging" : false,
		'ordering': true,
		'order' : [[4, 'desc']],
		"columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ]
	});
	t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
});

</script>
@endsection