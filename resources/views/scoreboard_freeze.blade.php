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
<style>
	td {
		transition: all 1s linear;
	}
</style>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
	$("table").DataTable({
		"paging": false,
		"ordering": true,
	});
	if ($("#magic-btn")) {
		$("#magic-btn").click(function() {
            $.ajax({
				url: "/scoreboard/get_the_last_team/" + {{ Auth::user()->selected_assignment_id }},
				type: "GET",
				dataType: "json",
				success: function(response) {
					let lastTeam = response.lastTeam 
					let lastProb = response.lastProb
					let ordering = response.ordering
					let cellProb = ordering + 5

					// create html dom from scoreboard_json
					let scoreboard_json = response.scoreboard
					var domParser = new DOMParser()
					var scoreboard = domParser.parseFromString(scoreboard_json, "text/html")

					// get score in scoreboard and update to scoreboard_freeze
					// update score of each problem
					var scoreboard_table = scoreboard.getElementsByClassName("table")[0]
					for (let i = 1; i < scoreboard_table.rows.length - 5; i++)
					{
						if (scoreboard_table.rows[i].cells[1].innerText == lastTeam)
						{
							score = scoreboard_table.rows[i].cells[cellProb].innerHTML
							classBS = scoreboard_table.rows[i].cells[cellProb].className
							break
						}
					}
					var rowIndex = -1
					$("table tr").each(function() {
						if ($(this).find("td:eq(1)").text() == lastTeam) {
							rowIndex = $(this).index() + 1
						}
					})
					$('table tr:eq("' + rowIndex + '") td:eq("' + cellProb + '")').html(score).prop("className", classBS)

					// update total score and total accepted
					var total_score_prev = parseInt($('table tr:eq("' + rowIndex + '") td:eq(4)').find("span").first().text())
					var score_int = parseInt($('table tr:eq("' + rowIndex + '") td:eq("' + cellProb + '")').find("a").text())
					var total_accepted = parseInt($('table tr:eq("' + rowIndex + '") td:eq(5)').find("span").first().text()) + 100
					var total_score = total_score_prev + score_int
					$('table tr:eq("' + rowIndex + '") td:eq(4)').find("span").first().text(total_score)
					if (score_int == 100)
						$('table tr:eq("' + rowIndex + '") td:eq(5)').find("span").first().text(total_accepted)
				}
            })
        })
		$("#interval-btn").click(function() {
			var interval = setInterval(function() {
				$("#magic-btn").click()
			}, 1000)
		})
	}
})

</script>
@endsection