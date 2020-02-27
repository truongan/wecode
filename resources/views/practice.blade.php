@extends('layouts.app')
@section('head_title','Practice')
@section('icon', 'fas fa-folder-open')

@section('title', 'Practice')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
@endsection

@section('title_menu')
<th>Practice</th>
@endsection

@section('content')
@section('content')
<div class="row">
    <div class="col">
        <div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>#</th>
						<th>ID</th>
						<th>Name</th>
						<th>Lang</th>
						<th>No. Submission</th>
						<th>No. Accepted Submission</th>
						<th>Tag</th>
					</tr>
				</thead>
				
				@foreach ($problems as $problem)
				<tr data-id="{{$problem->id}}">
					<td>{{$loop->iteration}}</td>
					<td>{{$problem->id}}</td>
					<td>{{$problem->name}}</td>
					<td>
						@foreach ($problem->lang as $lang_name)
							<p>{{$lang_name->name}}</p>
						@endforeach
					</td>
					<td>{{$problem->total_submission}}</td>
					<td>{{$problem->accepted_submission}}</td>
					<td>
						@foreach ($problem->tag as $tag_name)
							<p>{{$tag_name->text}}</p>
						@endforeach
					</td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>
@endsection

@section('body_end')
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function () {
    $("table").DataTable({
		"pageLength": 10,
		"lengthMenu": [ [10, 20, 30, 50, -1], [10, 20, 30, 50, "All"] ]
	});
});
</script>
@endsection