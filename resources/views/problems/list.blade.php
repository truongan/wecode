@extends('layouts.app')
@php($selected="instructor_panel")
@section('head_title','Problems')
@section('icon', 'fas fa-clipboard-list')
@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css'/>
@endsection
@section('title','Problems')

@section('title_menu')
{{-- {% if user.level >= 2 %} --}}
<span class="title_menu_item"><a href="{{ route('problems.create') }}"><i class="fas fa-plus fa-lg color8"></i> Add</a></span>
<span class="title_menu_item"><a href="{{ route('submissions.index',[0, Auth::user()->id, 'all', 'all'] ) }}"><i class="fas fa-list-ul fa-lg color8"></i>Review test submissions for problems</a></span>
{{-- <span class="title_menu_item"><a href="{{ url('problems/download_all') }}"><i class="fas fa-download fa-lg color8"></i>Download all problem's test and description</a></span> --}}
@endsection

@section('content')
<div class="row">
	<form class="form-inline" method="get" action="{{ route('problems.index') }}">
		<div class="row row-cols-auto g-2 align-items-end">
			<label for="search">Search by name</label>
			<div class="col ">
				<input type="text" name="search" id="search" class="form-control" placeholder="Search by name" aria-describedby="Search by name" value="{{ Request::get('search') }} " >
			</div>
			<div class="col">
				<button type="button" class="btn btn-danger" onClick="document.getElementById('search').value = '' ;"><i class="fas fa-times    "></i></button>
				<button type="submit" class="btn btn-primary">Search</button>
			</div>
		</div>
	</form>
	<div class="table-responsive">
	@error('messages')
		@php( $msgclasses = array('text-success'=> 'text-success', 'text-info'=> 'text-warning', 'text-danger'=> 'text-danger') )
		{{-- @php(dd($errors->get('messages'))) --}}
		{{-- @php(dd($message['type'])) --}}
		@foreach ($errors->get('messages') as $msg)
			<p class="text-danger">{{ $msg }}</p>
		@endforeach
	@enderror
	<table class="table table-striped table-bordered">
		<thead class="thead-old table-dark">
			<tr>
				<th>ID</th>
				<th style="width: 20%">Name</th>
				<th style="width: 20%">Note</th>
				<th>owner</th>
				<th>Tags</th>
				<th>Lang</th>
				<th><small>Assignmnet</small></th>
				<th><small>Submission</small></th>
				<th>Misc</th>
				<th>Tools</th>
			</tr>
		</thead>
	  @foreach ($problems as $item)
		<tr data-id="{{$item->id}}">
			<td>{{ $item->id}}</td>
			<td><a href="{{ url("problems/$item->id") }}">{{ $item->name }}</a></td>
			<td>{{$item->admin_note}}</td>
			<td>
				<span data-bs-toggle="tooltip"  
					@if($item->sharable) class="text-success"  title="publicly shared problem"
					@else class="text-secondary" title="Private problem"
					@endif
				>
					{{$item->user->username ?? 'no-owner'}}
				</span>

			</td>
			<td>
				@foreach ($item->tags as $tag)
			  		<span class="badge rounded-pill bg-info">{{$tag->text}}</span>
			  	@endforeach
			</td>
			<td>
				<a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#language_list_{{$item->id}}" aria-expanded="false" aria-controls="language_list_{{$item->id}}">
					{{  $item->languages->pluck('name')->join(", ")  }}
				</a>
			<div class="collapse" id="language_list_{{$item->id}}">
				
			  @foreach ($item->languages as $language_name)
			  	<span class="btn btn-sm btn-secondary mb-1">{{$language_name->name}} <span class="badge rounded-pill bg-info">{{$language_name->pivot->time_limit/1000}}s</span><span class="badge rounded-pill bg-info">{{$language_name->pivot->memory_limit/1000}}MB</span></span>
			  @endforeach
			</div>
			</td>
			<td>
					<a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#assignment_list_{{$item->id}}" aria-expanded="false" aria-controls="assignment_list_{{$item->id}}">
						{{ $item->assignments->count()}}<small> assignments</small>
					</a>
				<div class="collapse" id="assignment_list_{{$item->id}}">
					
					@foreach ($item->assignments as $assignment)
						<a href="{{ route('submissions.index', ['assignment_id' => $assignment->id, 'problem_id' => $item->id, 'user_id' => 'all' , 'choose' => 'all']) }}" >
						<span class="btn  btn-secondary btn-sm my-1">{{$assignment->name}} <span class="badge bg-info">{{$assignment->user->username ?? "no-owner"}}</span> </span></a>
					@endforeach
				</div>

			</td>
			<td>
				<span class="text-success">{{ $item->accepted_submit }}</span> 
				/
				<span class="text-info">{{ $item->total_submit }} ({{ $item->ratio }}%) </span>
			</td>
			<td>  
				
				<i  style="cursor:pointer" class="toggle_practice fas fa-dumbbell fa-2x clickable .stretched-link
					@if( $item->allow_practice)
						text-success
					@else
						text-secondary
					@endif
				"  data-bs-toggle="tooltip" data-id='{{$item->id}}'  title='This problem is available for practice'>
				</i>
				@if( $item->sharable)
					<i class="fas fa-share-alt" data-bs-toggle="tooltip" title='This problem is shared among instructors'></i>
				@endif
				@if($item->editorial != '')
					<a href="{{ $item->editorial }}" data-bs-toggle="tooltip" title='This problem has some linked editorial'><i class="fas fa-lightbulb fa-2x   "></i></a>
				@endif
				
				@if($item->author != '')
					<br/><i class="fas fa-user   "></i> {{$item->author}}
				@endif
			</td>
			
			<td>
				<a href="{{ route('problems.downloadtestsdesc',$item->id) }}">
					<i title="Download Tests and Descriptions" class="fa fa-cloud-download-alt fa-lg color11"></i>
				</a>
				<a href="{{ route('problems.edit', $item) }}"> <i title="Edit" class="far fa-edit fa-lg color3"> </i> </a>
				<span title="Delete problem" class="del_n delete_tag pointer">
				  <i title="Delete problem" class="far fa-trash-alt fa-lg color1"></i>
				</span>
			  
			</td>
		
		</tr>
	  @endforeach
	</table>
	<div class=" d-flex justify-content-center">{{$problems->links(null, ['class'=>'justify-content-center'])}}</div>
	</div>
</div>

<div class="modal fade" id="problem_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this tag?</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
		  	<div class="modal-footer">
				<button type="button" class="btn btn-danger confirm-tag-delete">YES</button>
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal">NO</button>
		  	</div>
		</div>
	</div>
</div>
@endsection


@section('body_end')

<script type='text/javascript' src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>
/**
* Notifications
*/
  $(document).ready(function () {
	$('.del_n').click(function () {
	  var row = $(this).parents('tr');
	  var id = row.data('id');
		$(".confirm-tag-delete").off();
		$(".confirm-tag-delete").click(function(){
		  $("#problem_delete").modal("hide");
			$.ajax({
			  type: 'DELETE',
			  url: '{{ route('problems.index') }}/'+id,
			  data: {
						  '_token': "{{ csrf_token() }}",
			  },
			  error: shj.loading_error,
			  success: function (response) {
				if (response.done) {
				  row.animate({backgroundColor: '#FF7676'},100, function(){row.remove();});
				  $.notify('problem deleted'	, {position: 'bottom right', className: 'success', autoHideDelay: 5000});
				  $("#problem_delete").modal("hide");
				}
				else
				  shj.loading_failed(response.message);
			  }
			});
		});
	  $("#problem_delete").modal("show");
	});

	$("table").DataTable({
		"paging": false,
		{{-- "ordering": false, --}}
		'order':[[0, 'desc']]
	});
	document.querySelector('.dataTables_filter > label').childNodes[0].data = "Filter in this page"
  });

	document.querySelectorAll('.toggle_practice').forEach( item => {
		item.addEventListener('click', (ev) => {
			var icon = ev.target;
			console.log(icon);
			$.ajax({
				type: 'POST',
				url: '{{ route('problems.toggle_practice') }}/'+ icon.dataset.id,
				data: {
							'_token': "{{ csrf_token() }}",
				},
				error: shj.loading_error,
				success: function (response) {
					console.log(response);
					console.log(icon);
					icon.classList.remove('text-success');
					icon.classList.remove('text-secondary');
					if (response == '1') {
						icon.classList.add('text-success');
					}
					else if (response == ''){
						icon.classList.add('text-secondary');
					}
					else
						shj.loading_failed(response.message);
				}
			});
		}, false)
	} )
</script>

<script>

</script>
@endsection

