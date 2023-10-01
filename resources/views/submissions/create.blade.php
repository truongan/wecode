{{--Code editer--}}
@php
$selected = 'assignments'
@endphp

@extends('layouts.app')
@section('head_title','Code editer')
@section('other_assets')
<link rel="stylesheet" type='text/css' href="{{ asset('assets/styles/submit_page.css') }}"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('icon', 'fas fa-code')
@section('title')</i> Assignment: {{ $assignment->name }}@endsection
	
@section('content')
{{-- <div class="row"> --}}
	@if (isset($error) && $error != 'none')
	<p class="text-warning"> {{ $error }}</p>
	@else

	{{-- <div class="row"> --}}
		<form action="{{ route('submissions.store') }}" method="POST" class = "">
		@csrf
			<input type ="hidden" id="assignment_id_input" value="{{$assignment->id}}" name="assignment"/>

		<input type="hidden" value="{{ $problem->id }}" name="problem"/>
		
		<div class='row row-cols-auto g-4 align-items-end'>
		
			<div class="col">
				<label for="problems" class="">Problem:<small class="form-text text-info"><a id="problem_link" href="#" target="_blank">View Problem statement</a></small></label>

				<select id="problems" name ="problem" class="form-select ">
					@if($assignment->id == 0)
						<option value="{{ $problem->id }}" data-statement="{{ route('practices.show', $problem->id) }}">#{{$problem->id}}--{{ $problem->name }}</option>
					@endif

					@foreach ($assignment->problems as $prob)
						<option value="{{ $prob->id }}" data-statement="{{ route('assignments.show', [ 'assignment' =>$assignment->id , 'problem_id' => $prob->id])}}">#{{$prob->id}}--{{ $prob->pivot->problem_name }}</option>
					@endforeach
				</select>


				@error('problem')
					<div class="alert alert-danger"> {{ $message }}</div>
				@enderror
			</div>

			<div class="col form-floating ">
				<select id="languages" name="language" class="form-select"></select>
				<label for="languages" class="">Language:</label>
				@error('language')
					<div class="alert alert-danger"> {{ $message }}</div>
				@enderror
			</div>

			<div class="col form-floating">
				<select id="theme" class="form-select">
					<option value="ambiance">ambiance</option>
					<option value="chaos">chaos</option>
					<option value="chrome">chrome</option>
					<option value="clouds">clouds</option>
					<option value="clouds_midnight">clouds_midnight</option>
					<option value="cobalt">cobalt</option>
					<option value="crimson_editor">crimson_editor</option>
					<option value="dawn">dawn</option>
					<option value="dreamweaver">dreamweaver</option>
					<option value="eclipse">eclipse</option>
					<option value="github">github</option>
					<option value="idle_fingers">idle_fingers</option>
					<option value="iplastic">iplastic</option>
					<option value="katzenmilch">katzenmilch</option>
					<option value="kr_theme">kr_theme</option>
					<option value="kuroir">kuroir</option>
					<option value="merbivore">merbivore</option>
					<option value="merbivore_soft">merbivore_soft</option>
					<option value="mono_industrial">mono_industrial</option>
					<option value="monokai">monokai</option>
					<option value="pastel_on_dark">pastel_on_dark</option>
					<option value="solarized_dark">solarized_dark</option>
					<option value="solarized_light">solarized_light</option>
					<option value="sqlserver">sqlserver</option>
					<option value="terminal">terminal</option>
					<option value="textmate">textmate</option>
					<option value="tomorrow">tomorrow</option>
					<option value="tomorrow_night">tomorrow_night</option>
					<option value="tomorrow_night_blue">tomorrow_night_blue</option>
					<option value="tomorrow_night_bright">tomorrow_night_bright</option>
					<option value="tomorrow_night_eighties">tomorrow_night_eighties</option>
					<option value="twilight">twilight</option>
					<option value="vibrant_ink">vibrant_ink</option>
				</select>
				<label for="code" class="">Code theme:</label>
			</div>

			<div class="ms-auto">
				<input type="submit" value="Submit" class="btn btn-primary btn-lg float-end"/>
			</div>
		</div>

		<div class="row editor-row mt-3">
			<div class="" id="banned" style="display: none;"></div>

			<div class="template-grp" id="before-grp" style="display: none;">
				<label for="">Template's header, <small>these lines will goes before your code</small></label>
				<div class="template" id="before" >abc</div>
			</div>

			<div class="editor-grp" id="editor_grp" style="display: none;">
				<label for="">Your code</label>
				<div id="editor">{{ $last_code ?? "Write your code here"}}</div>
			</div>

			<div class="template-grp" id="after-grp" style="display: none;">
				<label for="">Template's footer, <small>These lines will goes after your code</small></label>
				<div class="template" id="after" >def</div>
			</div>
		</div>
		
		<textarea style="display:none;" rows="4" cols="80" name="code" class="sharif_input add_text" >
		</textarea>
		</form>
	@endif

@endsection

@section('body_end')
<script type="text/javascript" src="{{ asset('assets/ace/ace.js') }}" charset="utf-8"></script>
<script type="text/javascript" src="{{ asset('assets/js/submit_page.js')   }}" charset="utf-8"></script>
<script>
	problem_languages ={};
	
	@if($assignment->id == 0)
		problem_languages[{{$problem->id}}] = {!!$problem->languages !!};
	@endif




	@foreach($assignment->problems as $prob)

		problem_languages[{{$prob->id}}] = {!! $prob->languages()->whereIn('language_id', explode(", ", $assignment->language_ids))->get() !!}

	@endforeach
	
	get_template_route = '{{ route('submissions.get_template') }}';

	$(document).ready(function(){
		///Select the problem from referring page

		$("select#problems").val({{ $problem->id }});
		$("select#problems").change();
	});
</script>
@endsection