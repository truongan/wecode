@php($selected = 'settings')
@extends('layouts.app')
@section('head_title','Admin panel')
@section('icon', 'fas fa-sliders-h')

@section('title', 'Admin panel')

@section('other_assets')
<style>
.card{
  width:9rem;
}
</style>
@endsection

@section('content')
<div class="col-12">
	<h1 class="display-5">What is Moss?</h1>
	<p>
		<a href="http://theory.stanford.edu/~aiken/moss/" target="_blank">Moss</a> (for a Measure Of Software Similarity)
		is an automatic system for determining the similarity of programs.
		To date, the main application of Moss has been in detecting plagiarism in programming classes. Since its
		development in 1994, Moss has been very effective in this role. The algorithm behind moss is a significant
		improvement over other cheating detection algorithms.
	</p>

	<br>

	<h3>Moss user id</h3>
	@if ($moss_userid == -1)
		<p class="text-danger">You have not entered your Moss user id.</p>
	@endif
	<p>
		Read <a href="http://theory.stanford.edu/~aiken/moss/" target="_blank">this page</a> and register for Moss,
		then find your user id in the script sent to your email by Moss and enter your user id here.
	</p>
	<form action="{{route('moss.update', $moss_assignment['id'])}}" method="POST">
		@csrf
		<div class="form-inline">
			<div class="form-group">
				<label for="moss_uid">Your Moss user id is:</label>
				<div class="input-group">
					<input id="moss_uid" type="text" name="moss_userid" class="form-control" 
					@if ($moss_userid == -1)
						value=""
					@else
						value="{{$moss_userid}}"
					@endif
					/>
					<span class="input-group-btn">
						<input type="submit" class="btn btn-info" value="Save"/>
					</span>
				</div>
			</div>
		</div>
	</form>

	<br>

	<h3>Detect similar submissions of assignment "<span>{{ $moss_assignment['name'] }}</span>":</h3>
	<p>
	<form action="{{route('moss.detect', $moss_assignment['id'])}}" method="POST">
		@csrf
		<input type="hidden" name="detect" value="detect" />
		You can send final submissions of assignment "<span>{{ $moss_assignment['name'] }}</span>" to Moss
		by clicking on this button.<br>
		Zip and PDF files will not be sent.<br>
		It may take a minute. Please be patient.<br>
		<input type="submit" class="sharif_input" value="Detect similar codes"/>
	</form>
	</p>

	<br>

	<h3>Moss results for assignment "<span>{{ $moss_assignment['name'] }}</span>":</h3>
	<p>
		Links will expire after some time. (last update: {{ $update_time }}) <br>
		<ul>
		@foreach ($moss_problems as $moss_problem)
			<li>Problem {{$loop->iteration}}:
				@if ($moss_problem == null)
					Link Not Found.
				{{-- @elseif (not $moss_problem|trim)
					Link Not Found. There were error running moss --}}
				@else
					<a href="{{ $moss_problem }}" target="_black">{{ $moss_problem }}</a>
				@endif
			</li>
		@endforeach
		</ul>
	</p>
</div>
@endsection