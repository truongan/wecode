@extends('layouts.app')
@php($selected="problems")
@section('icon', 'fas fa-puzzle-piece')

@section('title',$problem->name)

@section('other_assets')
<style media="screen">
    .wecode_table td{
        text-align: left;
    }
</style>
@endsection

@section('title_menu')
{{-- {% if problem.has_pdf %}
	<span class="title_menu_item"><a href="
	{% if all_problems is defined %}
		{{ site_url("view_problem/pdf/#{problem.id}/#{assignment.id}") }}
	{% else %}
		{{ site_url("problems/pdf/#{problem.id}") }}
	{% endif %}
	"><i class="fas fa-file-pdf color1"></i> PDF</a></span>
{% endif %}
{% if problem.has_template %}
	<span class="title_menu_item"><a href="{{ site_url("view_problem/template/#{problem.id}/#{assignment.id}") }}"><i class="fa fa-download color1"></i> Download the code template</a></span>
{% endif %}
{% if user.level >= 2 %}
	<span class="title_menu_item ml-auto"><a href="#" class="btn btn-secondary save-button"><i class="fa fa-floppy-o "></i> Save</a></span>
{% endif %} --}}
@endsection

@section('body_end')
<script type="text/x-mathjax-config">
    MathJax.Hub.Config({
      tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}
    });
    </script>
    <script type="text/javascript" async
      src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.2/MathJax.js?config=TeX-MML-AM_CHTML">
    </script>
    <script src="{{ asset('assets/ckeditor/ckeditor.js') }}" charset="utf-8"></script>
    <script type="text/javascript">
    $(document).ready(function(){
    
        shj.setup_save('.save-button'
        , '{{ url("problems/edit_description/$problem->id") }}'
        , CKEDITOR.instances.problem_description);
    
    });
</script>
@endsection

@section('content')
@if ($problem->error != 'none' )
<div class="alert alert-danger"> {{$error}} </div>
@else

	<div class="col-md-7 col-lg-8 col-sm-12">

        <div class="problem_description" id="problem_description" 
        {{-- {% if user.level >= 2 %} contenteditable="true" {% endif %}  --}}
        >
			{{ $problem->description }}
		</div>
	</div>

	<div class="col-md-5 col-lg-4 ">
		@if ($all_problems != NULL)
		@php($i = 0)
		<div class="problems_widget row">
			<p><i class="fa fa-file-text fa-lg color9"></i> { $assignment->name }}</p>
			{{-- <p class="text-muted"><span class="badge badge-secondary count_problems">{{ all_problems|length }}</span> problems with a total score of <span class="badge badge-secondary sum_score">{{ sum_score}}</span></p> --}}
			<table class="wecode_table table  table-bordered">
				<thead>
				<tr>
					<th>#</th>
					<th>Problem</th>
					<th>Score</th>
				</tr>
				</thead>
				@foreach($all_problems as $one_problem)
				@php( $i = $i + 1 )
					<tr class=" {{ $problem->id == $one_problem->id ? "table-active":"" }} ">
						<td>{{ $i }}</td>
						<td>
							@php($t = $assignment != NULL ?$assignment->id:"")
							<a href="{{ url("view_problem/$t/$one_problem->id") }}">{{ $one_problem->problem_name }}</a>
						</td>
						<td  class="{{ $problem_status[$one_problem->id] }}"><span>{{ $one_problem->score }}</span></td>
					</tr>
				@endforeach
			</table>
		</div>
		@endif


		@if ($can_submit)
		<div class="problems_widget ">

			<span><i class="fa fa-upload fa-lg color11"></i> Submit</span>
			
			{{-- {{ form_open_multipart("submit/") }} --}}

			@if ($all_problems != NULL)
				<input type="hidden" name="assignment" value="{ $assignment->id }}"/>
			@endif
			<input type="hidden" name="problem" value="{{ $problem->id }}"/>
			<fieldset class="form-group form-row">
				<label>
					Select language
				</label>

				<select id="languages" name="language" class="form-control custom-select">

					@foreach($problem->languages as $l)
                        <option value="{{ $l->id }}">{{ $l->name }}</option>
					@endforeach
				</select>
			</fieldset>

			<fieldset class="form-group form-row">
				<div class="col-12 custom-file">
					<input type="file" id="file" class=" custom-file-input" name="userfile"/>
					<label class="custom-file-label text-muted"><small>upload source code</small></label>
				</div>
			</fieldset>

			<fieldset class="form-group form-row">
				<input type="submit" value="Submit" class="form-control"/>
			</fieldset>
			</form>

		</div>
		<div class="problems_widget row">
			@php($t = $assignment != NULL ?$assignment->id:"")
			<span class=""><a href="{{ url("submit/editor/$problem->id/$t") }}" target="_blank"><i class="fa fa-pencil-square-o"></i> Code editor</a></span>
		</div>
		@endif

	</div>
@endif
@endsection