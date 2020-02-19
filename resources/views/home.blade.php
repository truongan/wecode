@extends('layouts.app')

@section('icon', 'fa fa-tachometer-alt')

@section('title', 'Dashboard')

@section('other_assets')
<link rel="stylesheet" type='text/css' href="{{ asset('css/fullcalendar/fullcalendar.min.css') }}">
@endsection

@section('body_end')
<script type='text/javascript' src="{{ asset('assets/fullcalendar/fullcalendar.js') }}"></script>
<script type='text/javascript' src="{{ asset('assets/js/jquery.autoellipsis-1.0.10.min.js') }}"></script>
{{-- <script>
$(document).ready(function () {
	$('#calendar').fullCalendar({
		timeFormat: 'HH:mm { - HH:mm}',
		editable: false,
		height: "auto",
		firstDay: {{ week_start }},
		events: [
			{% set colors = ['#812C8C','#FF750D','#2C578C','#013440','#A6222C','#42758C','#02A300','#BA6900'] %}
			{% for assignment in all_assignments %}
				{id:{{ assignment.id }},title:'{{ assignment.name|e('js') }}', start:'{{ assignment.start_time }}', end:' {{ assignment.finish_time }}',
				allDay:false,color:'{{ colors[(loop.index0)%colors|length] }}'}
			{% if not loop.last %},{% endif %}
			{% endfor %}
		]
	});
});
</script> --}}

{{-- Dưới đây là thẻ script tạm thời --}}
<script>
    $(document).ready(function () {
        $('#calendar').fullCalendar({
            timeFormat: 'HH:mm { - HH:mm}',
            editable: false,
            height: "auto",
            firstDay: 1,
            events: [
                                    ]
        });
        
        //$('.notif_text').ellipsis();
    });
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="shj_widget">
              <div class="widget_title"><i class="fa fa-calendar-o fa-lg color10"></i> Calendar</div>
            <div class="widget_contents_container" id='calendar'></div>
          </div>
    </div>
    
    <div class="col-md-6">
        <div class="shj_widget">
              <div class="widget_title"><i class="fa fa-bell-o fa-lg color2"></i>
                  Latest Notifications
              </div>
            <div class="widget_contents_container">
                {{-- {% if notifications|length == 0 %}
                    <p style="text-align: center;">Nothing yet...</p>
                {% endif %} --}}
                @foreach ($notifications as $notification)
                    <div class="notif" id="number{{ $notification->id }}" data-id="{{ $notification->id }}">
                        <div class="notif_title">
                        <span class="anchor ttl_n">{{ $notification->title }} - {{$notification->user->username}}</span>
                            <span class="notif_meta" dir="ltr">
                            {{ $notification->created_at }}
                            </span>
                        </div>
                        <div class="notif_text latest">{!! $notification->description !!}</div>
                    </div>
                @endforeach
            </div>
          </div>
    </div>
</div>

@endsection