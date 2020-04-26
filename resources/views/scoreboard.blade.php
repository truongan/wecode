@extends('layouts.app')

@section('icon', 'fas fa-star')

@section('title', 'Scoreboard')

@section('title_menu')
{{-- thêm assignment.id vào --}}
@php($place="full")
@php($sl = 0)
@if (isset(Auth::user()->selected_assignment_id))
	@php($sl = 1)
@endif
<small>
	<ul class="nav nav-pills">
		<li class="nav-item">
			<a class="nav-link {{$place=="full" ? "active" :""}}"
			@if ($sl)
                href="{{ route('scoreboards.index', Auth::user()->selected_assignment_id) }}"
            @else
                href="{{ route('scoreboards.index', 0 )}}"
            @endif
			>
			<i class="fas fa-star color10"></i> Full information </a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{$place=="simplify" ? "active" :""}}" 
			@if ($sl)
                href="{{ route('scoreboards.index', Auth::user()->selected_assignment_id) }}"
            @else
                href="{{ route('scoreboards.index', 0 )}}"
            @endif
			>
			<i class="fas fa-star-half-alt color10"></i> Minimal information </a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{$place=="plain" ? "active" :""}}" 
			@if ($sl)
                href="{{ route('scoreboards.index', Auth::user()->selected_assignment_id) }}"
            @else
                href="{{ route('scoreboards.index', 0 )}}"
            @endif
			>
			<i class="fas fa-star-half-alt color10"></i> Plain text Minimal </a>
		</li>
	</ul>
</small>
@endsection
@section('content')
<div class="col">
	@if (isset($assignment->id) && $assignment->id == 0)
	<p>No assignment is selected.</p>
	@elseif (!isset($assignment->score_board) && in_array( Auth::user()->role->name, ['admin', 'head_instructor']))
	{{-- level<2???? --}}
	<p>Scoreboard is disabled.</p>
	@else
		<p>Scoreboard of <span> {{ $assignment->name }}</span></p>
		{!! $scoreboard !!}
	@endif
</div>
@endsection
