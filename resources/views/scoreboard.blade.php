@extends('layouts.app')

@section('icon', 'fas fa-star')
@section('head_title', 'Scoreboard')
@section('title', 'Scoreboard')

@section('other_assets')
<link rel='stylesheet' type='text/css' href='{{ asset('assets/DataTables/datatables.min.css') }}'/>
<script>
	if(!!window.performance && window.performance.navigation.type === 2)
	{
		window.location.reload();
	}
</script>
@endsection

@section('title_menu')
{{-- thêm assignment.id vào --}}

@php($sl = 0)
@if (isset(Auth::user()->selected_assignment_id))
	@php($sl = 1)
@endif
<ul class="ms-4 fs-6 nav nav-pills">
	<li class="nav-item">
		<a class="nav-link link-dark {{$place=="full" ? "active" :""}}"
			href="{{ route('scoreboards.index', $assignment->id) }}">
		<i class="fas fa-star color10"></i> Full information </a>
	</li>
	<li class="nav-item">
		<a class="nav-link link-dark {{$place=="simplify" ? "active" :""}}"
			href="{{ route('scoreboards.simplify', $assignment->id) }}">
		<i class="fas fa-star-half-alt color10"></i> Minimal information </a>
	</li>
	<li class="nav-item">
		<a class="nav-link link-dark {{$place=="plain" ? "active" :""}}"
			href="{{ route('scoreboards.plain', $assignment->id) }}">
		<i class="fas fa-star-half-alt color10"></i> Plain text Minimal </a>
	</li>
</ul>
@endsection


@section('content')
<div class="mx-n2">
	@if (isset($assignment->id) && $assignment->id == 0)
	<p>No assignment is selected.</p>
	@elseif (!isset($assignment->score_board) && in_array( Auth::user()->role->name, ['admin', 'head_instructor']))
	{{-- level<2???? --}}
	<p>Scoreboard is disabled.</p>
	@else
		<p>Scoreboard of  <span> {{ $assignment->name }} {for: {{ $assignment->lops->pluck('name')->join(",") }} - by:  {{$assignment->user->username ?? "no-owner"}}) </span></p>
		<div class="table-responsive">
			{!! $scoreboard !!}
		</div>
		<span class="text-danger">*: Not full mark</span>
		<br/>
		<span class="text-info">Number of tries - Submit time</span>
		<br/>
		<span class="text-warning">**: Delay time</span>
	@endif
</div>
@endsection

@section('body_end')

<script type="text/javascript" src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
	$("table").DataTable({
		"paging": false,
		"ordering": true,
	});
});
</script>
@endsection
