@php($selected = 'settings')
@extends('layouts.app')
@section('head_title','Edit Problem')
@section('icon', 'fas fa-tags')

@section('title', 'Edit Problem')

@section('title_menu')
	<span class="title_menu_item"><a href="{{ route('problems.index') }}"><i class="fa fa-list color11"></i>Back to list of problems</a></span>
@endsection
@section('content')

@if($messages)
	@foreach ($messages as $item)
		<p style="color:red;">{{$item}}</p>	
	@endforeach
@endif

<div class="row">
  	<form action="{{route('problems.update', $problem->id)}}" method="POST" enctype="multipart/form-data">
		@method('PUT')
		@csrf
		{{-- upload folder --}}
		<fieldset class="form-group">
			<div class="form-row">
				<div class="col-sm-6">
					<label for="form_tests_dir"><i class="far fa-lg fa-folder-open"></i>Tests and Descriptions (folder)
						<br/>
					<small class="text-secondary">You can upload an entire folder of <strong> {{ 1000 }} </strong> file(s).
							If your test folder have more files you will have to upload a zip file of that folder instead. Also, this features is not web standard, some browser may not support it </small>
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

		{{-- upload zip  --}}
		{{-- <div class="col-sm-6"> --}}
			{{-- <fieldset class="form-group">
				<div class="form-row">
					<label for="admin_note">Admin's note</label>
					<textarea id="admin_note" name="admin_note" rows="3" class="form-control add_text">{{ edit_problem ? edit_problem.admin_note : set_value('admin_note', "", false) }}</textarea>
						{{ form_error('admin_note', '<div class="alert alert-danger">', '</div>') }}
				</div>
			</fieldset>
			<fieldset class="form-group" data-toggle="tooltip" data-html="true" title="Rename all files in <strong>in</strong> and <strong>out</strong> folder after unziping. This is assuming that all files in these two folder are perfectly corresponding to each when sorted by file name. This could useful when importing dataset from another format but should be use with care ">
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="customSwitch1" name="rename_zip">
					<label class="custom-control-label" for="customSwitch1">Re-order files in <strong>in</strong> and <strong>out</strong> folder after upload<br/><small>This could useful when importing dataset from another format but should be use with care </small>
					</label>
				</div>
			</fieldset> --}}
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
		</div>
		
	  
	
		<p>problem name</p>
			<input type="text"
				name="name" id="name" value={{$problem->name}}>
		<p>admin note</p>
			<input type="text"
				name="admin_note" id="name" value={{$problem->admin_note}}>
		<p>diff arg</p>
			<input type="text"
				name="	diff_arg" id="name" value={{$problem->diff_arg}}>
		<p>diff cmd</p>
			<input type="text"
				name="diff_cmd" id="name" value={{$problem->diff_cmd}}>
		
		@foreach ($languages as $item)
			<p> edit language {{$item->name}} </p>
				<input type="text" name="enable[]">
				<input type="hidden" name = "language_update[]" value={{$item->id}}>
				
				<input type="text"
					name="time_limit[]" id="name" value={{$item->default_time_limit}}>
				<input type="text"
					name="memory_limit[]" id="name" value={{$item->default_memory_limit}}>
		@endforeach
		<button type="submit" class="btn btn-primary">Edit</button>
	</form>
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
@endsection
