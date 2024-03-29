@extends('layouts.app')
@php($selected = 'settings')
@section('head_title','Rejudge')
@section('icon', 'fas fa-redo')

@section('title', 'Rejudge')

@section('content')
<div class="mx-n2">		
	<p>
		Selected Assignment: <span>{{ $assignment->name }}</span>
		<form action="{{ route('submissions.rejudge_all_problems_assignment') }}" method="POST">
		@csrf
			<input type="hidden" name="problem_id" value="all"/>
			<input type="submit" class="btn btn-primary m-1" value="Rejudge All Problem"/>
		</form>
	</p>
	<p>
		By clicking on rejudge, all submissions of selected problem will change to <code>PENDING</code> state. Then
		Sharif Judge rejudges them one by one.
	</p>
	<p>
		If you want to rejudge a single submission, you can click on rejudge button in <a href="{{ route('submissions.index', [$assignment->id, 'all', 'all', 'all']) }}">All Submissions</a> or <a href="{{ route('submissions.index', [$assignment->id, 'all', 'all', 'final']) }}">Final Submissions</a> page.
	</p>
	@foreach ($problems as $problem)
		<form action="{{ route('submissions.rejudge_all_problems_assignment') }}" method="POST">
		@csrf
			<input type="hidden" name="problem_id" value="{{ $problem->id }}"/>
			<input type="hidden" name="assignment_id" value="{{ $assignment->id }}"/>
			<input type="submit" class="btn btn-primary m-1" value="Rejudge Problem {{ $problem->id }} ({{ $problem->pivot->problem_name }})"/>
		</form>
	@endforeach

	@if (\Session::has('status'))
	    <div class="alert alert-info fade show" role="alert">
		  	{!! \Session::get('status') !!}.
		  	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@endif
	

</div>
@endsection