@extends('layouts.app')
@section('head_title','Dashboard')
@section('icon', 'fa fa-tachometer-alt')

@section('title', 'Dashboard')

@section('other_assets')
<link rel="stylesheet" type='text/css' href="{{ asset('assets/fullcalendar/main.min.css') }}">
<style>
    .card {
        width: 12rem;
        height: 100%;
    }
</style>
@endsection

@section('body_end')
<script type='text/javascript' src="{{ asset('assets/fullcalendar/main.min.
js') }}"></script>

{{-- Dưới đây là thẻ script tạm thời --}}
<script>
    $(document).ready(function () {
        var all_assignments = @json($all_assignments);
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            displayEventTime: true,
            editable: false,
            height: "auto",
            firstDay: 1,

            events: [
                @php($colors = ['#812C8C', '#FF750D', '#2C578C', '#013440', '#A6222C', '#42758C',
                    '#02A300', '#BA6900'
                ])
                @foreach($all_assignments as $assignment) {
                    id: {{$assignment-> id}},
                    title: '{{ $assignment->name }}',
                    start: '{{ $assignment->start_time }}',
                    end: '{{ $assignment->finish_time }}',
                    allDay: false,
                    color: '{{ $colors[($loop->index)%count($colors)] }}',
                    url: '{{  route('assignments.show',['assignment'=>$assignment,'problem_id'=>0]) }}',
                },
                @endforeach
            ]
        });
        calendar.render();
        //$('.notif_text').ellipsis();
    });

</script>
@endsection

@section('content')
@if ( in_array( Auth::user()->role->name, ['admin']) )

<div class="row">
    <div class="card-group ">
        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-cogs fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">SETTING</strong></small>
                    <small>
                        <p class="card-text">Chỉnh sửa và ...</p>
                    </small>
                    <a href="{{ route('settings.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        {{-- <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-users fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">USERS</strong></small>
                    <small>
                        <p class="card-text">Quản lý người dùng</p>
                    </small>
                    <a href="{{ route('users.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-clipboard-list fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">User statistics</strong></small>
                    <small>
                        <p class="card-text">Thống kê tình hình submit của user</p>
                    </small>
                    <a href="{{ route('users.rank') }}" class="stretched-link"></a>
                </div>
            </div>
        </div> --}}
        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-laptop-code fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">LANGUAGES</strong></small>
                    <small>
                        <p class="card-text">Thiết lập ngôn ngữ lập trình</p>
                    </small>
                    <a href="{{ route('languages.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-play fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">Submission Queue</strong></small>
                    <small>
                        <p class="card-text">Những thao tác xử lý trên hàng đợi các bài đang chấm</p>
                    </small>
                    <a href="{{ route('queue.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
<div class="row">
    <div class="card-group ">
        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-users fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">USERS</strong></small>
                    <small>
                        <p class="card-text">Quản lý người dùng</p>
                    </small>
                    <a href="{{ route('users.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-clipboard-list fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">User statistics</strong></small>
                    <small>
                        <p class="card-text">Thống kê tình hình submit của user</p>
                    </small>
                    <a href="{{ route('users.rank') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-tags fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">TAGS</strong></small>
                    <small>
                        <p class="card-text">Nhãn dán cho các problems</p>
                    </small>
                    <a href="{{ route('tags.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-edit fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">EDIT BY HTML</strong></small>
                    <small>
                        <p class="card-text">Trình soạn thảo đề bài (problem description) trên web</p>
                    </small>
                    <a href="{{ route('htmleditor') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="m-2">
            <div class="card bg-dark text-light">
                <i class="text-center card-img-top fas fa-user-secret fa-2x p-3"></i>
                <div class="card-body bg-light text-dark">
                    <small><strong class="card-title">Detect Similar Codes</strong></small>
                    <small>
                        <p class="card-text">Kiểm tra code trùng nhau</p>
                    </small>
                    <a href="{{ route('moss.index' , Auth::user()->selected_assignment_id) }}"
                        class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

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
                @if (count($notifications) == 0)
                <p style="text-align: center;"></p>
                @endif
                @foreach ($notifications as $notification)
                <div class="notif" id="number{{ $notification->id }}" data-id="{{ $notification->id }}">
                    <div class="notif_title">
                        <span class="anchor ttl_n">{{ $notification->title }} - {{$notification->user->display_name}}</span>
                        <span class="notif_meta" dir="ltr">
                            {{ $notification->created_at }}
                        </span>
                    </div>
                    <div class="notif_text latest">{!! $notification->text !!}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
