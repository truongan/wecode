@extends('layouts.app')
@php($selected="instructor_panel")
@section('head_title','Problems')
@section('icon', 'fas fa-clipboard-list')
@section('other_assets')
  <link rel='stylesheet' type='text/css' href='{{ asset('assets/DataTables/datatables.min.css') }}'/>
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/select2/select2.min.css') }}">
  {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/select2/select2-bootstrap-5-theme.min.css') }}"> --}}
<style>
	.top-search-bar > .select2-container {
		flex: 1 1;
	}
	.select2-container  textarea {
		color: black;
	}
</style>
@endsection
@section('title','Problems')

@section('title_menu')
{{-- {% if user.level >= 2 %} --}}
<a href="{{ route('problems.create') }}"><span class="ms-4 fs-6 text-dark-subtle"><i class="fas fa-plus fa-lg color8"></i> Add</span></a>
<a href="{{ route('submissions.index',[0, Auth::user()->id, 'all', 'all'] ) }}"><span class="ms-4 fs-6 text-dark-subtle"><i class="fas fa-list-ul fa-lg color8"></i>Review practice submissions</span></a>
@endsection

@section('content')
<div class="row">

	<div class="accordion  accordion-flus" id="accordionExample">
		<div class="accordion-item">
		  <h2 class="accordion-header">
			<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
			  Import/Export problems
			</button>
		  </h2>
		  <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
			<div class="accordion-body">
				<div class="row mb-2">
					<div class="col-md"><a href="" download id = "download_all_selected"><i class="fas fa-download fa-lg text-info"></i>Export selected problem (zip)</a></div>
					<div class="col-md"><a href="" id="select_all_for_download"><i class="fas fa-check-square fa-lg text-info"></i>Select all problems</a></div>
					<div class="col-md"><a href="" id="deselect_all_for_download"><i class="far fa-square fa-lg text-info"></i>Deselect all problems</a></div>
					<div class="col-md-6">
						<form 		enctype="multipart/form-data"			action="{{ route('problems.import') }}"  method="post"  class="row g-3 needs-validation" novalidate>
						  <div class="col-md-10">
							<label for="validationCustom05" class="form-label">Import multiple (zipped) problems</label>
							<input name="zip_upload" type="file" class="form-control-sm" id="validationCustom05" required>
						  </div>
						  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
						  <div class="col-2">
							<button class="btn btn-primary" type="submit">Import</button>
						  </div>
						</form>
					</div>
				</div>
			</div>
		  </div>
		</div>

	<form class="row mb-3 gx-3  align-items-center" method="get" action="{{ route('problems.index') }}">
		<div class=" col-5">
			<div class="input-group">
				<label class="input-group-text" for="search">Search by name</label>
				<input type="text" name="search" id="search" class="form-control" placeholder="Search by name" aria-describedby="Search by name" value="{{ Request::get('search') }} " >
				<button type="button" class="btn btn-outline-danger" onClick="document.getElementById('search').value = '' ;"><i class="fas fa-times    "></i></button>
			</div>
		</div>
		<div class="col-6">
			<div class="input-group  top-search-bar">
				<label class="input-group-text"> and by tag(s)</label>
				<select class="search-by-tags form-control"multiple="multiple" name="tag_id[]">
				</select>
			</div>
		</div>
		<div class="col-1">
			<button type="submit" class="btn btn-primary form-control">Search</button>
		</div>
	</form>



	<div class="table-responsive">
	@error('messages')
		@php( $msgclasses = array('text-success'=> 'text-success', 'text-info'=> 'text-warning', 'text-danger'=> 'text-danger') )
		@foreach ($errors->get('messages') as $msg)
			<p class="text-danger">{{ $msg }}</p>
		@endforeach
	@enderror
	<table class="table table-striped table-bordered">
		<thead class="thead-old table-dark">
			<tr>
				<th>owned?</th>
				<th>ID?</th>
				<th style="width: 20%">Name</th>
				<th style="width: 55%">Note</th>
				<!-- <th>owner</th> -->
				<th style="width: 15%">Tags</th>
				<th>Lang</th>
				<th>Date</th>
				<th><small>Assignmnet</small></th>
				<th><small>Submission</small></th>
				<th>Misc</th>
				<th>Tools</th>
			</tr>
		</thead>
	  @foreach ($problems as $item)
		<tr data-id="{{$item->id}}">
			<td>{{ $item->user->username == Auth::user()->username }}</td>
			<td>{{ $item->id}}</td>
			{{-- NAME --}}
			<td>
			    <a href="{{ route( 'practices.show' ,$item->id) }}"> <span class="badge text-bg-info"> {{ $item->id}}</span> {{ $item->name }}</a>
				<br/>
				by:
				<span
				    class="badge rounded-pill
					@if($item->sharable) text-bg-success
					@else text-bg-secondary
					@endif
					"
				>
					{{$item->user->username ?? 'no-owner'}}
				</span>
			</td>
			{{-- NOTE --}}
			<td class="fs-6 "> {{$item->admin_note}}</td>

			{{-- TAGS --}}
			<td>
				<div class="holder-for-one-problem-tags">
					@foreach ($item->tags as $tag)
						<span class="badge rounded-pill bg-info" data-id="{{$tag->id}}">{{$tag->text}}</span>
					@endforeach
				</div>
				@if($item->can_edit(Auth::user()))
					<form action="{{ route('problems.edit_tags', $item->id) }}" method="post" class="edit-tag-form d-none">
						<select  multiple="multiple" class="form-control edit-tag-list"></select>
						<button type="button" class="btn btn-small btn-danger tags-edit-cancel"><i class="fas fa-window-close"></i></button>
						<button type="submit" class="btn btn-small btn-primary" ><i class="fa fa-check" aria-hidden="true"></i></button>
					<span class ="text-secondary text-small">Use '#' character to add new tag</span>
					</form>
					<span  class = "edit-tag-list-handle"> <i title="Edit tag list" class="far fa-edit fa-lg text-warning"> </i> </span>
				@endif
			</td>
			{{-- LANG --}}
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

			{{-- Date --}}
			<td>
				Created: {{ $item->created_at ?  $item->created_at->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('YYYY/MM/DD HH:mm:ss') : 'Unknown' }}
				Modified: {{ $item->created_at ?  $item->updated_at->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('YYYY/MM/DD HH:mm:ss') : 'Unknown' }}
			</td>
			{{-- ASSIGNMENTS --}}
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
			{{-- SUBMISSIONS --}}
			<td>
				<span class="text-success">{{ $item->accepted_submit }}</span>
				/
				<span class="text-info">{{ $item->total_submit }} ({{ $item->ratio }}%) </span>
			</td>
			{{-- SHARE, PRACTICE, EDITORIAL --}}
			<td>

				<i  style="cursor:pointer" data-bs-toggle="tooltip" data-id='{{ "toggle." .  $item->id}}'  title='Green icon means this problem is available for practice, Black icon for otherwise' class="toggle_practice-share fas fa-dumbbell fa-2x clickable .stretched-link
					@if( $item->allow_practice)
						text-success

					@else
						text-body-tertiary

					@endif
				">
				</i>
				<i style="cursor:pointer" data-bs-toggle="tooltip" data-id='{{'share.' . $item->id}}' title='Green icon means this problem is shared among instructors. Black icon means it is only visible  to its author and admins'  class="toggle_practice-share fas fa-share-alt fa-2x clickable .stretched-link
				@if( $item->sharable)
					text-success

				@else
					text-body-tertiary
				@endif
				"">
				</i>

				@if($item->editorial != '')
					<a href="{{ $item->editorial }}" data-bs-toggle="tooltip" title='This problem has some linked editorial'><i class="fas fa-lightbulb fa-2x   "></i></a>
				@endif

				@if($item->author != '')
					<br/><i class="fas fa-user   "></i> {{$item->author}}
				@endif
			</td>
			{{-- DOWNLOAD, EDIT, DELETE --}}
			<td>
				<div class="form-check">
					<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault-{{$item->id}}" data-id="{{$item->id}}">
					<label class="form-check-label" for="flexCheckDefault-{{$item->id}}"
						<i title="Select for download" class="fa fa-cloud-download-alt fa-lg text-success"></i>
					</label>
				  </div>
				@if($item->can_edit(Auth::user()))
					<a href="{{ route('problems.edit', $item) }}"> <i title="Edit" class="far fa-edit fa-lg color3"> </i> </a>
					<span title="Delete problem" class="del_n delete_tag pointer">
					<i title="Delete problem" class="far fa-trash-alt fa-lg text-danger"></i>
					</span>
				@endif
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
<script type="text/javascript" src="{{ asset('assets/select2/select2.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
<script>

	var all_tags = {!! $all_tags !!};
	// alert(all_tags);

	function populate_search(){
		document.querySelector(".search-by-tags").textContent = '' ;
		$(".search-by-tags").append(all_tags.map(i=> new Option(i.text, i.id, false, false)));
		$(".search-by-tags").val( {!! json_encode(Request::get('tag_id')) !!} ).trigger('change');
	}

  document.addEventListener("DOMContentLoaded", function () {

	$(".search-by-tags").select2({
		closeOnSelect: false
	});
	populate_search();

	document.querySelectorAll('.edit-tag-list-handle').forEach(
		i => i.addEventListener('click'
			, () => {
				// console.log(event.currentTarget);
				var tag_list_div = event.currentTarget.parentElement.querySelector('.holder-for-one-problem-tags')
				tag_id_list = [...tag_list_div.querySelectorAll('span')].map(i => i.dataset.id);

				var tag_edit_form = event.currentTarget.parentElement.querySelector('form')
				tag_edit_form.classList.remove('d-none');

				var select_element = tag_edit_form.querySelector('select');
				select_element.textContent = '';
				var select_obj = $(select_element).select2({
					tags:true,
					tokenSeparators: [','],
					closeOnSelect: false,
					createTag: (params) => {
						var term = $.trim(params.term);

						if (term === '') return null;
						if (term[0] != '#') return null;

						return {
							id: term,
							text: term,
							newTag: true // add additional parameters
						}
					},
				});
				select_obj.append(all_tags.map(i=> new Option(i.text, i.id, false, false)));
				select_obj.val(tag_id_list);
				// console.log(tag_id_list);

				tag_list_div.classList.add('d-none')
				event.currentTarget.classList.add('d-none');
			})
	);
	document.querySelectorAll('.tags-edit-cancel').forEach(
		i => i.addEventListener('click', ()=>{
			// console.log(event.currentTarget);
			event.currentTarget.parentElement.classList.add('d-none');
			event.currentTarget.parentElement.parentElement.querySelector('.holder-for-one-problem-tags').classList.remove('d-none');
			event.currentTarget.parentElement.parentElement.querySelector('.edit-tag-list-handle').classList.remove('d-none');
		})
	)
	document.querySelectorAll('.edit-tag-form').forEach(
		i => i.addEventListener('submit', (event)=>{
			event.preventDefault();

			var select_obj = $(event.currentTarget.querySelector('select'));
			fetch(event.currentTarget.action, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				},
				body: JSON.stringify({
					tag_id: select_obj.val(),
				})
			}).then( response =>  response.json()
			).then((data) => {
				all_tags = data.all_tags;

				event.target.parentElement.querySelector('.holder-for-one-problem-tags').innerHTML = data.new_tags.map(i => '<span class="badge rounded-pill bg-info" data-id="'+i.id+'">'+i.text+'</span>').join('');
				event.target.querySelector('.tags-edit-cancel').click();
				populate_search();
			}).catch(error => console.log("Error", error));

		})
	);

	document.querySelectorAll('#select_all_for_download')[0].onclick = (event) => {
		event.preventDefault();
		document.querySelectorAll('.form-check-input').forEach(select => select.checked = true);
	};

	document.querySelectorAll('#deselect_all_for_download')[0].onclick = (event) => {
		event.preventDefault();
		document.querySelectorAll('.form-check-input').forEach(select => select.checked = false);
	};

	document.querySelectorAll('#download_all_selected')[0].onclick = (event) => {
		// event.preventDefault();
		// fetch(
		// 	'{{ route("problems.export") }}',
		// 	{
		// 		method: 'POST',
		// 		headers: {
		// 			'Content-Type': 'application/json',
		// 			'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		// 		},
		// 		body: JSON.stringify({
		// 			'ids' : [...document.querySelectorAll('.form-check-input:checked')].map(i => i.dataset['id']),
		// 		}),
		// 	}
		// )
		// .then(response => response.blob())
		// .then(blob => {

		// });

		event.target.href = '{{ route('problems.export') }}?'
			+ new URLSearchParams(
				{
					'ids' : [...document.querySelectorAll('.form-check-input:checked')].map(i => i.dataset['id'])
				}
			).toString();
	};

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
		columnDefs:[
		    {
				target: 0,
				visible : false
			}
		    ,{
				target: 1,
				visible : false
			}
		],
		{{-- "ordering": false, --}}
		'order':[
		    [0, 'desc']
			,[1, 'desc']
		]
	});
	document.querySelector('.dataTables_filter > label').childNodes[0].data = "Filter in this page"
  });

	document.querySelectorAll('.toggle_practice-share').forEach( item => {
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
					icon.classList.remove('text-body-tertiary');
					if (response == '1') {
						icon.classList.add('text-success');
					}
					else if (response == ''){
						icon.classList.add('text-body-tertiary');
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
