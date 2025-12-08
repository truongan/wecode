@php($selected = 'instructor_panel')
@php($edit = Route::currentRouteName() == 'problems.edit')

@extends('layouts.app')
@section('head_title','New Problem')
@section('icon', 'fas fa-plus-square')

@section('title', 'New Problem')

@section('other_assets')

<link rel="stylesheet" type="text/css" href="{{ asset('assets/slimselect/slimselect.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/slimselect/an.slimselect.bootstrap.hack.css') }}">

@endsection

@section('title_menu')
<span class="ms-4 fs-6">
	<a href="{{ route('problems.index') }} " target="_blank"><i class="fa fa-list text-danger"></i> List of problems</a>
</span>
<span class="ms-4 fs-6">
	<a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/add_assignment.md" target="_blank"><i class="fa fa-question-circle text-danger"></i> Help</a>
</span>
@endsection

@section('body_end')
<script type="text/javascript" src="{{ asset('assets/slimselect/slimselect.js') }}"></script>

<script type="text/javascript">
	document.querySelectorAll(".add_language").forEach( x => x.addEventListener('click', function(){
		var lang = this.dataset['lang'];
		console.log(lang);
		this.classList.toggle('d-none');

		var row = document.querySelector(".lang_row_" + lang);
		row.classList.toggle('d-none');//.show();
		row.querySelector('.lang_checkbox').value = 1;
	}));

	document.querySelectorAll(".remove_language").forEach( x => x.addEventListener("click", function(){
		var lang = this.dataset['lang'];
		var row = document.querySelector(".lang_row_" + lang);
		row.classList.toggle('d-none');//.show();
		row.querySelector('.lang_checkbox').value = 0;

		document.querySelector('.add_language_' + lang).classList.toggle('d-none');
	}));

	document.addEventListener("DOMContentLoaded", function(){
		@if (!$edit)
		lang = document.querySelectorAll('.add_language');
		for(var i = 0; i < {{$settings['default_language_number']}} ; i++){
			if (i > lang.length) break;
			lang[i].click();
		}
		@endif
	});

	var select_obj = new SlimSelect({
		select : ".js-example-tokenizer",
		events: {
			addable : (params) => {
				var term = params.trim();
				if (term === '') return false;
				if (term[0] != '#') return false;

				return term;
			},
		}
	});


</script>
@endsection

@section('content')

<div class=" ">
	@php( $msgclasses = array('text-success'=> 'text-success', 'text-info'=> 'text-warning', 'text-danger'=> 'text-danger') )

	@foreach ($messages as $message)
		<p class="{{ $msgclasses[$message->type] }}">
			{{ $message->text }}
		</p>
	@endforeach

	@if ($edit)
	<p>
		<i class="fa fa-info-circle fa-lg color8"></i> If you don't want to change tests or pdf file, just do not upload its file.
	</p>
	@endif


	<form method="POST"
		@if ($edit)
			action="{{ route('problems.update', $problem) }}"
		@else
			action="{{ route('problems.store') }}"
		@endif
		enctype="multipart/form-data"
	>
	@if ($edit)
		@method("PUT")
	@endif
	@csrf
	<div class="row mb-4">
		<div class="col-sm-6 mb-3">
			<div class="row gy-3">
				<div class="form-floating">
					<input id="name" type="text" name="name" class="form-control col-xs-7" value="{{ old('name',  $edit ? $problem->name : '') }}"/>
					@error('name')
						<div class="alert alert-danger" role="alert">
							<strong>{{ $message }}</strong>
						</div>
					@enderror
					<label for="name">Problem Name</label>
				</div>
				<div class="form-floating">
					<input id="author" type="text" name="author" class="form-control col-xs-7" value="{{ old('author',  $edit ? $problem->author : '') }}"/>
					@error('author')
						<div class="alert alert-danger" role="alert">
							<strong>{{ $message }}</strong>
						</div>
					@enderror
					<label for="author">Original author
					</label>
					<small class="text-secondary">Honor original author by writing his/her name and affiliation here.</small>
				</div>

				<div class="form-floating">
					<input id="editorial" type="text" name="editorial" class="form-control col-xs-7" value="{{ old('editorial',  $edit ? $problem->editorial : '') }}"/>
					@error('editorial')
						<div class="alert alert-danger" role="alert">
							<strong>{{ $message }}</strong>
						</div>
					@enderror
					<label for="editorial">Link to editorial
					</label>
					<small class="text-secondary">Provide a link to editorial here</small>
				</div>
				<div class="d-none">
					<div class="col-sm-5">
						<label for="diff_cmd">Diff command</label>
					</div>
					<div class="col-sm-7">
						<input  type="text" name="diff_cmd" class="form-control col-xs-7" value="{{ old('diff_cmd', $edit ? $problem->diff_cmd : 'diff') }}"/>
					</div>
				</div>
				<div class="d-none">
					<div class="col-sm-5">
						<label for="diff_arg">Diff arguments</label>
					</div>
					<div class="col-sm-7">
						<input  type="text" name="diff_arg" class="form-control col-xs-7" value="{{ $edit ? $problem->diff_arg : old('diff_arg', '-bB') }}"/>
					</div>
				</div>
				<div class="just-for-gutter">
					<label for="form_tests_dir"><i class="far fa-lg fa-folder-open">
					</i>Tests and Descriptions (folder)
					</label>
					<input id="form_tests_dir" type="file" webkitdirectory  multiple name="tests_dir[]" class="form-control" />
					<small class="text-secondary">You can upload an entire folder of <strong> {{ $max_file_uploads }} </strong> file(s).
						If your test folder have more files you will have to upload a zip file of that folder instead. Also, this features is not web standard, some browser may not support it
					</small>
				</div>

				<div class="just-for-gutter">
					<label>Difficult</label>
					<div class="row  small">
						<div class="col">
							1
						</div>
						<div class="col-6 text-end">
							5
						</div>
					</div>
					<input type="range" class="form-range" min="1" max="5"  name="difficult" value="{{ old('difficult',  $edit ? $problem->difficult : '') }}">
					<output class="bubble"></output>
				</div>

				<div class="just-for-gutter">
					<label>Tag(s)</label>
					<small class="text-secondary">You can add new tag by precedding them with '#' character (this character will not be present in the tag's text)</small>
					<select class="js-example-tokenizer form-control" multiple="multiple" name="tag_id[]">
						@foreach( $all_tags as $t)
						<option value="{{ $t->id }}" data-text="{{$t->text}}" data-id="{{$t->id}}"
							{{ isset($tags[$t->id]) ? 'selected="selected"' : ''  }}
							>{{$t->text}}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="form-floating mb-3">
				<textarea id="admin_note" name="admin_note" rows="4" class="form-control add_text" style="height: 10em" >{{ $edit ? $problem->admin_note : old('admin_note', "", false) }}</textarea>
				<label for="admin_note">Admin's note</label>
				{{-- {{ form_error('admin_note', '<div class="alert alert-danger">', '</div>') }} --}}
			</div>

			<div class="row g-3 mt-4">
				<div class="form-check-inline  form-check form-switch col">
					<input type="checkbox" class="form-check-input" id="customSwitch2" name = "allow_practice" {{ ($edit ? $problem->allow_practice : old('allow_practice', 0)) ? 'checked' : '' }} >
					<label class="form-check-label" for="customSwitch2">
						Allow practice <small class="text-secondary">Allow other users to see this problem in practice view</small>
					</label>
				</div>
				<div class="form-check-inline form-check form-switch col">

					<input type="checkbox" class="form-check-input" id="sharable_switch" name = "sharable"  {{ ($edit ? $problem->sharable : old('sharable', 0)) ? 'checked' : '' }} >
					<label class="form-check-label" for="sharable_switch">
						Sharable <small class="text-secondary">Allow other head_instructor to view this problem and use it in theirs assignments</small>
					</label>
				</div>
			</div>
			<div class="row g-3 mt-2">
				<div class="form-check-inline form-check form-switch col">

					<input value="1" type="checkbox" class="form-check-input" id="allow_input_download_switch" name = "allow_input_download"  {{ ($edit ? $problem->allow_input_download : old('allow_input_download', 0)) ? 'checked' : '' }} >
					<label class="form-check-label" for="allow_input_download_switch">
						Alow input download <small class="text-secondary">Allow anyone that can submit to the problems to be able to download the input from testcases as well</small>
					</label>
				</div>
				<div class="form-check-inline form-check form-switch col">

					<input value="1" type="checkbox" class="form-check-input" id="allow_output_download_switch" name = "allow_output_download"  {{ ($edit ? $problem->allow_output_download : old('allow_output_download', 0)) ? 'checked' : '' }} >
					<label class="form-check-label" for="allow_output_download_switch">
						Allow output download <small class="text-secondary">Allow anyone that can submit to the problems to be able to download the output from testcases as well</small>
					</label>
				</div>
			</div>
			<div class="row mt-4">

				<div class="form-check-inline  form-check form-switch m-t-3">
					<input type="checkbox" class="form-check-input" id="customSwitch1" name="rename_zip">
					<label class="form-check-label" for="customSwitch1">Re-order files in <strong>in</strong> and <strong>out</strong> folder after upload<br/><small>This could useful when importing dataset from another format but should be use with care </small>
					</label>
				</div>
				<div class="my-4">
					<label for="form_tests_zip"><i class="far fa-lg fa-file-archive"></i>Tests and Descriptions (zip file)</label>
					<input id="form_tests_zip" type="file" name="tests_zip" class="form-control" />
					<small class="text-secondary">Folder upload will always take precedent, if you want to upload zip file, leave upload folder field blank. </small>
				</div>
				<p>
					<a class="btn btn-primary btn-small" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
						Show current problem directory structure
					</a>
				</p>
				<div class="collapse" id="collapseExample">
					<div class="card card-body">
						<pre>
							{{ $tree_dump }}
						</pre>
					</div>
				</div>
			</div>
		</div>
	</div>


	<label >Click on the button to add more language for this problems</label></br>
	{{-- <div class ="form-old-row row"> --}}
		@foreach($all_languages as $lang)
			<a data-lang="{{ $lang->id }}" class="btn btn-success me-2 add_language add_language_{{ $lang->id }}
				{{ isset($languages[$lang->id]) ? "d-none" : "" }}" href="#!" role="button">
				{{ $lang->name }}
			</a>
		@endforeach
	{{-- </div> --}}

	<div class="form-old-row row">
		<div class="table-responsive">
			<table id="problems_table" class="mt-2 table">
				<thead class="thead-old ">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Time Limit (ms)</th>
					<th>Memory<br>Limit (kB)</th>
					<th>Remove</th>
				</tr>
				</thead class="form-inline">
				<tbody>
					@foreach( $all_languages as $lang )
					<tr class = "lang_row_{{ $lang->id }} {{ isset($languages[$lang->id]) ? "" : "d-none" }}">
						<input class="lang_checkbox" type="hidden" name="enable[]" value="{{ isset($languages[$lang->id]) ? "1" : "0" }}"/>
						<td>{{ $lang->id }} <input type="hidden" name="language_id[]" value="{{ $lang->id }}"></td>
						<td>{{ $lang->name }}</td>
						<td><input type="number" name="time_limit[]" class="form-control" value="{{ isset($languages[$lang->id]) ? $languages[$lang->id]->pivot->time_limit : $lang->default_time_limit}}"/></td>
						<td><input type="number" name="memory_limit[]" class="form-control" value="{{ isset($languages[$lang->id]) ? $languages[$lang->id]->pivot->memory_limit :  $lang->default_memory_limit }}"/></td>
						<td><a  data-lang="{{ $lang->id }}"  class="btn btn-danger remove_language remove_language_{{ $lang->id }}" href="#" role="button"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<input type="submit" value="{{ $edit ? 'Edit' : 'Add' }} Problem" class="sharif_input btn btn-primary mt-2"/>
</form>
</div>

@endsection
