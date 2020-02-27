@extends('layouts.app')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css'/>
@endsection

@section('title_menu')
@endsection



@section('content')

@if (isset($error) && $error != 'none')
<p class="text-warning"> {{ $error }}</p>
@else

	<form action="{{ route('submissions.store',['assignment_id'=> $assignment->id, 'problem_id' => $problem->id]) }}" method="POST">
	@csrf
	@if ($assignment->id != 0)
		
			<input type ="hidden" value="{{assignment.id}}" name="assignment"/>
	@endif

	<input type="hidden" value="{{ $problem->id }}" name="problem"/>
	<div class="form-inline">
			<div class="form-group mr-3">
				<label for="problems" class="">Problem:</label>
				<select id="problems" name ="problem" class="form-control custom-select">
					@foreach ($assignment->problems as $problem)
						<option value="{{ $problem->id }}">#{{$problem->id}}--{{ $problem->problem_name }}
						</option>
					@endforeach
				</select>

				<small class="form-text text-info"><a id="problem_link" href="#" target="_blank">Problem statement</a>
				</small>
				@error('problem')
					<div class="alert alert-danger"> {{ $message }}</div>
				@enderror
			</div>
			<div class="form-group mr-3">
				<label for="languages" class="">Language:</label>
				<select id="languages" name="language" class="form-control custom-select">

				</select>
				@error('language')
					<div class="alert alert-danger"> {{ $message }}</div>
				@enderror
				
			</div>

			<div class="form-group">
				<label for="code" class="">Code theme:</label>
				<select id="theme" class="form-control custom-select">
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
			</div>
		{# </div> #}
	</div>
	<div class="" id="banned"></div>

	<div class="template-grp" id="before-grp">
		<label for="">Template's header, <small>these lines will goes before your code</small></label>
		<div class="template" id="before" >abc</div>
	</div>

	<div class="editor-grp">
		<label for="">Your code</label>
  		<div id="editor"></div>
	</div>

	<div class="template-grp" id="after-grp">
		<label for="">Template's footer, <small>These lines will goes after your code</small></label>
		<div class="template" id="after" >def</div>
	</div>


	<div class="form-group">
		<input type="submit" value="Submit" class="btn btn-primary float-right"/>
	</div>
	<textarea style="display:none;" rows="4" cols="80" name="code" class="sharif_input add_text" >
	</textarea>
	</form>

@endif
@endsection

@section('body_end')
<script src="{{ base_url('assets/ace/ace.js') }}" type="text/javascript" charset="utf-8"></script>
<script  type="text/javascript" src="{{ base_url('assets/js/submit_page.js')   }}" charset="utf-8"></script>
<script>
	shj.p={};
	
	@if (isset($assignment))
		shj.selected_assignment = {{assignment.id}};
	@else
		shj.selected_assignment = 0;
	@endif
	
	{{ problems_js|raw }}

	$(document).ready(function(){
		///Select the problem from referring page

		$("select#problems").val({{ from }});

		$("select#problems").change();
	});
</script>
@endsection