@php($selected = 'freeze')
@extends('layouts.app')
@section('icon', 'fas fa-snowflake')
@section('head_title', 'Scoreboard freeze')
@section('title', 'Scoreboard freeze')

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
@endsection


@section('content')
<div class="mx-n2">
	@if (isset($assignment->id) && $assignment->id == 0)
	<p>No assignment is selected.</p>
	@elseif (!isset($assignment->score_board))
	{{-- level<2???? --}}
	<p>Scoreboard is disabled.</p>
	@else
		@if (Carbon\Carbon::now() >= $assignment->freeze_time && Carbon\Carbon::now() < $assignment->unfreeze_time)
		<p>Scoreboard freeze of <span> {{ $assignment->name }}</span></p>
		<div class="table-responsive">{!! $scoreboard_freeze !!}</div>
		@else
		<p>Scoreboard of <span> {{ $assignment->name }}</span></p>
		<div class="table-responsive">{!! $scoreboard !!}</div>
		@endif
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
$(document).ready(function () {
	$("table").DataTable({
		"paging": false,
		"ordering": true,
	});
});

</script>
@endsection