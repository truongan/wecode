@extends('layouts.app')
@php($selected="settings")
@section('head_title','View User')
@section('icon', 'fas fa-users')

@section('title')
Users - ranking
@endsection
@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css'/>
@endsection
@section('content')

<form >
	<div class="form-floating">
		<textarea name="names" id="names" class="form-control" style="height:8em;" placeholder="Paste username list here" aria-describedby="help_names" value="">{{ Request::get('names') }}</textarea>
		<label for="names">Username lists</label>
		<small id="help_names" class="text-muted">A list of usernames, sperarated by white space to see ranking among them</small>
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
<table class="wecode_table table table-striped table-bordered table-sm">
	<thead class="thead-old table-dark">
		<tr>
			<th>#</th>
			<th>Username</th>
			<th><small>clases</small></th>
			<th>Problem solved</th>
			<th>Total submission</th>
			<th>No. accepted (Percentage)</th>
			<th>Problem tried</th>
			<th> Solved percentage - average tries to solve</th>
		</tr>
	</thead>
	@foreach ($users as $user)
		<tr>
			<td>{{$loop->iteration}}
			<td>
				<a href="{{ route('users.show', ['user' => $user->id]) }}"> {{$user->username}}
				</a>
      		</td>
			<td>
        @foreach ($user->lops as $lop)
            <a href="{{ route('lops.show', $lop->id) }}">{{$lop->name}}</a></br>
        @endforeach
			<td>
				{{ count($stats[$user->id]->solved_problems)}}
			</td>
			<td>
				{{ $stats[$user->id]->total }}
			</td>
			<td>
				{{ $stats[$user->id]->total_accept }} ({{@round(fdiv($stats[$user->id]->total_accept, $stats[$user->id]->total) * 100, 2) }} )%
			</td>
			<td> 
				{{ count($stats[$user->id]->problem_wise_stat)}}
			</td>
			<td> 
				{{ round(fdiv(count($stats[$user->id]->solved_problems)*100, count($stats[$user->id]->problem_wise_stat)),2) }}% - {{ round( fdiv(array_sum($stats[$user->id]->solved_problems), count($stats[$user->id]->solved_problems)) ,2 )  }}
			</td>
		</tr>
	@endforeach
</table>
@endsection

@section('body_end')


<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script>

$(document).ready(function(){
   	var t =  $("table").DataTable({
	  	"paging" : false,
		'ordering': true,
		'order' : [[3, 'desc']],
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