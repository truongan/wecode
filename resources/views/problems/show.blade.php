@extends('layouts.app')
@php ($selected ?? $selected="assignments")
@section('head_title','View Problem')
@section('icon', 'fas fa-puzzle-piece')

@section('title',isset($problem->name) ? $problem->name : 'Problem ...')

@section('other_assets')
<style media="screen">
    .wecode_table td{
        text-align: left;
    }
	#problem_pdf_embed{
		height: 50rem;
		border: 1rem solid rgba(0,0,0,.1);
	}
	#problem_description table  tr{
		border-width:1px;
	}
</style>
@endsection
@if (!isset($error))
@section('title_menu')

@if($problem->has_pdf)
	<a href="{{ route('problems.pdf',$problem->id) }}" class="link-dark"><span class="ms-4 fs-6"><i class="fas fa-file-pdf text-danger"></i> PDF</span></a>
@endif
@if ($problem->has_template)
	<span class="ms-4 fs-6"><a href="{{ route('problems.template', ['problem_id' => $problem->id, 'assignment_id' => ($all_problems != NULL ? $assignment->id : 'null')] ) }}" class="link-dark"><i class="fa fa-download text-danger"></i> Download the code template</a></span>
@endif
@if (in_array( Auth::user()->role->name, ['admin', 'head_instructor']))
	<span class="ms-4 fs-6 ms-auto"><a href="#" class="btn btn-info save-button"><i class="fa fa-floppy-o "></i> Save</a></span>
@endif 

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
		$('.save-button').click(function(){
            $.ajax({
                type: 'POST',
                url: '{{ route('problems.edit_description', $problem->id) }}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    content : CKEDITOR.instances.problem_description.getData()
                },
                success: function (response) {
                    if (response == "success"){
                        $.notify('Change sucessfully saved'
                            , {position: 'bottom right', className: 'success', autoHideDelay: 3500});
                        $('.save-button').removeClass('btn-info').addClass('btn-secondary');
                    }
                },
                error: function(response){
                    $.notify('Error while saving'
                        , {position: 'bottom right', className: 'error', autoHideDelay: 3500});
                }
            });
        }); 
    });
</script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.5/pdfobject.min.js" integrity="sha512-K4UtqDEi6MR5oZo0YJieEqqsPMsrWa9rGDWMK2ygySdRQ+DtwmuBXAllehaopjKpbxrmXmeBo77vjA2ylTYhRA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>PDFObject.embed("{{ route('problems.pdf',$problem->id) }}", "#problem_pdf_embed");</script> --}}

@endsection
@endif
@section('content')
@if (isset($error))
<div class="alert alert-danger"> {{$error}} </div>
@else
<div class="row">
	<div class="col-md-7 col-lg-8 col-sm-12">
		@if($problem->has_pdf)
			<div class="problem_description" id="problem_pdf_embed">
				<object data="{{ route('problems.pdf',$problem->id) }}" type="application/pdf" width="100%" height="100%">
					
					<p>If this browser does not support PDFs. Please download the PDF to view it: 
					<a href="{{ route('problems.pdf',$problem->id) }}">Download PDF</a>.</p>
				</object>
			</div>
		@endif
        <div class="problem_description" id="problem_description" 
        {{ in_array( Auth::user()->role->name, ['admin', 'head_instructor']) ? 'contenteditable=true' : ''}}
		>
			{!! $problem->description !!}
		</div>
	</div>

	<div class="col-md-5 col-lg-4 ">
		@if ($all_problems != NULL)
		@php($i = 0)
		<div class="problems_widget">
			@if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
			<a href="{{ route('assignments.edit', $assignment->id) }}" >
			@endif
				{{ $assignment->name }} <i class="fa fa-edit  color9"></i> <br/>
			@if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
			</a>
			@endif
			
			<p class="text-muted"><span class="badge bg-secondary count_problems">{{ count($all_problems) }}</span> problems with a total score of <span class="badge bg-secondary sum_score">{{ $sum_score }}</span></p>
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
							<a href="{{route('assignments.show', ['assignment'=>$assignment,'problem_id'=>$one_problem->id])}}">{{ $one_problem->pivot->problem_name }}</a>
						</td>
						<td  class="{{ isset($problem_status[$one_problem->id])? $problem_status[$one_problem->id] :'' }}"><span>{{ $one_problem->pivot->score }}</span></td>
					</tr>
				@endforeach
			</table>
		</div>
		@endif


		@if ($can_submit)
		<div class="problems_widget ">

			<span><i class="fa fa-upload fa-lg text-success"></i> Submit</span>
			
			<form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
			@csrf

			@if ($all_problems != NULL)
				<input type="hidden" name="assignment" value="{{ $assignment->id }}"/>
			@else 
			{{-- Default assignment to practice --}}
				<input type="hidden" name="assignment" value="0"/>
			@endif
			<input type="hidden" name="problem" value="{{ $problem->id }}"/>

			<div class="">
				<label class="custom-file-label text-muted"><small>upload source code</small></label>
				<input type="file" id="file" class=" form-control" name="userfile"/>
			</div>

			<div class="col-8">
				<div class="form-floating">
					<select id="languages" name="language" class="form-select">
						@foreach($problem->languages as $l)
							<option value="{{ $l->id }}">{{ $l->name }} ({{$l->pivot->time_limit /1000}}s, {{ $l->pivot->memory_limit / 1000 }}MB )</option>
						@endforeach
					</select>
					<label>Select language</label>
				</div>
			</div>
			<div class="col-4">
				<input type="submit" value="Submit" class="form-control btn btn-primary btn-lg"/>
			</div>
			</form>

		</div>
		<div class="problems_widget row">
			@php($t = $assignment->id ?? 0)
			<span class=""><a href="{{ route("submissions.create", ['assignment' => $t, 'problem' => $problem->id]) }}" target="_blank"><i class="fa fa-pencil-square-o"></i> Code editor</a></span>
		</div>
		@endif

	</div>
</div>
@endif


@endsection