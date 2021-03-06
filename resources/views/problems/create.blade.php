@php($selected = 'instructor_panel')
@php($edit = Route::currentRouteName() == 'problems.edit')

@extends('layouts.app')
@section('head_title','New Problem')
@section('icon', 'fas fa-plus-square')

@section('title', 'New Problem')

@section('other_assets')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/select2/select2.min.css') }}">
<style type="text/css">
	input[type='number']{
		min-width: 80px!important;
	}

	#choice_multi_assignment .select2-selection__choice{
		display:none !important;
	}
	span.select2{
		width: 100% !important;
	}
</style>
@endsection

@section('title_menu')
<span class="title_menu_item">
	<a href="{{ route('problems.index') }} " target="_blank"><i class="fa fa-list color1"></i> List of problems</a>
</span>
<span class="title_menu_item">
	<a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/add_assignment.md" target="_blank"><i class="fa fa-question-circle color1"></i> Help</a>
</span>
@endsection

@section('body_end')
<script type="text/javascript" src="{{ asset('assets/select2/select2.min.js') }}"></script>
<script type='text/javascript' src="{{ asset('assets/js/taboverride.min.js') }}"></script>
<script type="text/javascript">
	$(".add_language").click(function(){
		var lang = $(this).data('lang');
		console.log(lang);
		$(this).toggleClass('d-none');
		
		var row = $(".lang_row_" + lang);
		row.toggleClass('d-none');//.show();
		row.children('.lang_checkbox').val(1);
	});

	$(".remove_language").click(function(){
		var lang = $(this).data('lang');
		console.log(lang);
		var row = $(".lang_row_" + lang);
		row.toggleClass('d-none');//.show();
		row.children('.lang_checkbox').val(0);

		$('.add_language_' + lang).toggleClass('d-none');
	});

	$(document).ready(function(){
		tabOverride.set(document.getElementsByTagName('textarea'));
		$('.js-example-basic-multiple').select2();
		@if (!$edit)
		$lang = document.querySelectorAll('.add_language');
		for(var i = 0; i < {{$settings['default_language_number']}} ; i++){
			if (i > $lang.length) break;
			$lang[i].click();
		}
		@endif
	});
	$(".js-example-tokenizer").select2({
    tags: true,
    tokenSeparators: [',']
	})

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
	<div class="row">
		<div class="col-sm-6">
			<fieldset class="form-group">
				<div class="form-row">
					<div class="col-sm-5">
						<label for="name">Problem Name</label>
					</div>
					<div class="col-sm-7">
						<input id="name" type="text" name="name" class="form-control col-xs-7" value="{{ old('name',  $edit ? $problem->name : '') }}"/>
						@error('name')
							<div class="alert alert-danger" role="alert">
								<strong>{{ $message }}</strong>
							</div>
						@enderror
					</div>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<div class="form-row">
					<div class="col-sm-5">
						<label for="author">Original author 							<small class="text-secondary">Honor original author by writing his/her name and affiliation here.
							</small>
						</label>
					</div>
					<div class="col-sm-7">
						<input id="author" type="text" name="author" class="form-control col-xs-7" value="{{ old('author',  $edit ? $problem->author : '') }}"/>
						@error('author')
							<div class="alert alert-danger" role="alert">
								<strong>{{ $message }}</strong>
							</div>
						@enderror
					</div>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<div class="form-row">
					<div class="col-sm-5">
						<label for="editorial">Link to editorial <small class="text-secondary">Provide a link to editorial here
							</small></label>
					</div>
					<div class="col-sm-7">
						<input id="editorial" type="text" name="editorial" class="form-control col-xs-7" value="{{ old('editorial',  $edit ? $problem->editorial : '') }}"/>
						@error('editorial')
							<div class="alert alert-danger" role="alert">
								<strong>{{ $message }}</strong>
							</div>
						@enderror
					</div>
				</div>
			</fieldset>
			<fieldset class="form-group d-none">
				<div class="form-row">
					<div class="col-sm-5">
						<label for="diff_cmd">Diff command</label>
					</div>
					<div class="col-sm-7">
						<input  type="text" name="diff_cmd" class="form-control col-xs-7" value="{{ old('diff_cmd', $edit ? $problem->diff_cmd : 'diff') }}"/>
					</div>
				</div>
			</fieldset>
			<fieldset class="form-group d-none" >
				<div class="form-row">
					<div class="col-sm-5">
						<label for="diff_arg">Diff arguments</label>
					</div>
					<div class="col-sm-7">
						<input  type="text" name="diff_arg" class="form-control col-xs-7" value="{{ $edit ? $problem->diff_arg : old('diff_arg', '-bB') }}"/>
					</div>
				</div>
			</fieldset>			
			<fieldset class="form-group">
				<div class="form-row">
					<div class="col-sm-6">
						<label for="form_tests_dir"><i class="far fa-lg fa-folder-open"></i>Tests and Descriptions (folder)
	
							<small class="text-secondary">You can upload an entire folder of <strong> {{ $max_file_uploads }} </strong> file(s).
								If your test folder have more files you will have to upload a zip file of that folder instead. Also, this features is not web standard, some browser may not support it 
							</small>
						</label>
					</div>
					<div class="col-sm-6">
						<div class="custom-file">
							<input id="form_tests_dir" type="file" webkitdirectory  multiple name="tests_dir[]" class="custom-file-input" />
							<label class="custom-file-label text-muted"><small>Test cases and description folder</small></label>
						</div>
					</div>
				</div>
			</fieldset>
			
			<fieldset class="form-group">
				<div class="form-row">
					<div class="col-sm-4">
						<label>Select difficult</label>
					</div>
					<div class="col-sm-8">
						<div class="row">
							<div class="col-6">
								1
							</div>
							<div class="col-6 text-right">
								5
							</div>
						</div>
						<input type="range" class="custom-range" min="1" max="5" id="customRange2" name="difficult" value="{{ old('difficult',  $edit ? $problem->difficult : '') }}">
						<output class="bubble"></output>
					</div>
				</div>
			</fieldset>

			<fieldset class="form-group">
				<div class="form-row">
					<div class="col-sm-4">
						<label>Select tag(s)</label>
					</div>
					<div class="col-sm-8">
						<select class="js-example-tokenizer form-control" multiple="multiple" name="tag_id[]">
							@foreach( $all_tags as $t)
							<option value="{{ $t->id }}" data-text="{{$t->text}}" data-id="{{$t->id}}" 
								{{ isset($tags[$t->id]) ? 'selected="selected"' : ''  }}
								>{{$t->text}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-sm-6">
			<fieldset class="form-group">
				<div class="form-row">
					<label for="admin_note">Admin's note</label>
					<textarea id="admin_note" name="admin_note" rows="3" class="form-control add_text">{{ $edit ? $problem->admin_note : old('admin_note', "", false) }}</textarea>
						{{-- {{ form_error('admin_note', '<div class="alert alert-danger">', '</div>') }} --}}
				</div>
			</fieldset>
			
			<div class="row">
				<div class="col-sm-6">
					<fieldset class="form-group" data-toggle="tooltip" data-html="true" title="Add this problem into practice">
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="customSwitch2" name = "allow_practice" {{ ($edit ? $problem->allow_practice : old('allow_practice', 0)) ? 'checked' : '' }} >
							<label class="custom-control-label" for="customSwitch2">
								Allow practice <small class="text-secondary">Allow other users to see this problem in practice view</small>
							</label>
						</div>
					</fieldset>
				</div>
				<div class="col-sm-6">
					
					<fieldset class="form-group" data-toggle="tooltip" data-html="true" title="Add this problem into practice">
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="sharable_switch" name = "sharable"  {{ ($edit ? $problem->sharable : old('sharable', 0)) ? 'checked' : '' }} >
							<label class="custom-control-label" for="sharable_switch">
								Sharable <small class="text-secondary">Allow other head_instructor to view this problem and use it in theirs assignments</small>
							</label>
						</div>
					</fieldset>
				</div>
			</div>
			<fieldset class="form-group" data-toggle="tooltip" data-html="true" title="Rename all files in <strong>in</strong> and <strong>out</strong> folder after unziping. This is assuming that all files in these two folder are perfectly corresponding to each when sorted by file name. This could useful when importing dataset from another format but should be use with care ">
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="customSwitch1" name="rename_zip">
					<label class="custom-control-label" for="customSwitch1">Re-order files in <strong>in</strong> and <strong>out</strong> folder after upload<br/><small>This could useful when importing dataset from another format but should be use with care </small>
					</label>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<div class="form-row">
					<div class="col-sm-6">
						<label for="form_tests_zip"><i class="far fa-lg fa-file-archive"></i>Tests and Descriptions (zip file) </br>
							<small class="text-secondary">Folder upload will always take precedent, if you want to upload zip file, leave upload folder field blank. </small>
						</label>
					</div>
					<div class="col-sm-6">
						<div class="custom-file">
							<input id="form_tests_zip" type="file" name="tests_zip" class="custom-file-input" />
							<label class="custom-file-label text-muted"><small>Test case and description zip file</small></label>
						</div>
					</div>
				</div>
			</fieldset>
			<p>
				<a class="btn btn-primary btn-small" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
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
	

	<label>Click on the button to add more language for this problems</label></br>
	<div class ="form-row">
		@foreach($all_languages as $lang)
			<a data-lang="{{ $lang->id }}" class="btn btn-success mr-2 add_language add_language_{{ $lang->id }} 
				{{ isset($languages[$lang->id]) ? "d-none" : "" }}" href="#" role="button">
				{{ $lang->name }}
			</a>
		@endforeach
	</div>
	
	<div class="form-row"> 
		<div class="table-responsive">
			<table id="problems_table" class="mt-2 table">
				<thead class="thead-light">
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