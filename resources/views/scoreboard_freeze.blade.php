@php($selected = 'freeze')
@extends('layouts.app')
@section('icon', 'fas fa-snowflake')
@section('head_title', 'Scoreboard freeze')
@section('title', 'Scoreboard freeze')

@section('other_assets')
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css'/>
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
	@elseif (!isset($assignment->score_board) || !in_array( Auth::user()->role->name, ['admin', 'head_instructor']))
	{{-- level<2???? --}}
	<p>Scoreboard is disabled.</p>
	@elseif (in_array( Auth::user()->role->name, ['admin', 'head_instructor']))
		<p>Scoreboard of <span> {{ $assignment->name }}</span></p>
		<div class="table-responsive">{!! $scoreboard_freeze !!}</div>
		<span class="text-danger">*: Not full mark</span>
		<br/>
		<span class="text-info">Number of tries - Submit time</span>
		<br/>
		<span class="text-warning">**: Delay time</span>
	@endif
</div>
@endsection

@section('body_end')

<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
	$("table").DataTable({
		"paging": false,
		"ordering": true,
	});
	if ($("#magic-btn")) {
		$('#magic-btn').click(function() {
            $.ajax({
            url: '/scoreboard/get_the_last_team/' + {{ Auth::user()->selected_assignment_id }},
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let lastTeam = response.lastTeam 
                console.log(lastTeam)
            }
            })
        })
	}
})

</script>
@endsection