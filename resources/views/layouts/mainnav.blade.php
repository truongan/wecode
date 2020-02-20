<nav id="mainNav" class="mainNav navbar navbar-expand-lg navbar-dark fixed-top color-{{ $selected }} bg-dark">
        <a  class="navbar-brand" href="{{ url('/home') }}">
            <img src="{{ asset('images/logo.png') }}" height="30px" />
            {{ $settings['site_name'] }}
        </a>
    
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
        </button>
    
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav bg-dark navbar-sidenav color-{{ $selected }}" id="exampleAccordion">
                <li class="nav-item color-dashboard {{ ($selected=="dashboard") ? "selected" : ""}}">
                <a class="nav-link" href="{{ url('home') }}">
                    <i class="fa fa-fw fa-tachometer-alt fa-lg"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
                </li>
                <li class="nav-item color-notifications {{ ($selected=="notifications") ? "selected" : ""}}">
                    <a class="nav-link" href="{{ url('notifications') }}">
                        <i class="fa fa-fw fa-bell fa-lg"></i>
                        <span class="nav-link-text">Notifications</span>
                    </a>
                </li>
                @if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
                    
                    <li class="nav-item color-settings {{ ($selected=="settings") ? "selected" : ""}}" >
                        <a class="nav-link" href="{{ route('admin.index') }}">
                            <i class="fa fa-fw fa-sliders-h fa-lg"></i>
                            <span class="nav-link-text">Admin panel</span>
                        </a>
                    </li>

                @endif

                <li class="nav-item color-assignments {{ ($selected=="assignments") ? "selected" : ""}}">
                    <a class="nav-link" href="{{ url('assignments') }}">
                        <i class="fa fa-fw fa-folder-open fa-lg"></i>
                        <span class="nav-link-text">Assignments</span>
                    </a>
                </li>
                <li class="nav-item color-problems {{ ($selected=="problems") ? "selected" : ""}}">
                    <a class="nav-link" href="{{ url('view_problem') }}">
                        <i class="fa fa-fw fa-puzzle-piece fa-lg"></i>
                        <span class="nav-link-text">Problems</span>
                    </a>
                </li>
                <li class="nav-item color-submit {{ ($selected=="submit") ? "selected" : ""}}">
                    <a class="nav-link" href="{{ url('submit') }}">
                        <i class="fas fa-fw fa-code fa-lg"></i>
                        <span class="nav-link-text">Code Editor</span>
                    </a>
                </li>
                <li class="nav-item color-all_submissions {{ ($selected=="all_submissions") ? "selected" : ""}}">
                    <a class="nav-link" href="{{ url('submissions/all') }}">
                        <i class="fa fa-fw fa-bars fa-lg"></i>
                        <span class="nav-link-text">Submissions</span>
                    </a>
                </li>
                <li class="nav-item color-scoreboard {{ ($selected=="scoreboard") ? "selected" : ""}}">
                    <a class="nav-link" href="{{ url('scoreboard') }}">
                        <i class="fa fa-fw fa-star fa-lg"></i>
                        <span class="nav-link-text">Scoreboard</span>
                    </a>
                </li>

                <div class="p-1 sidenav-bottom nav-item mt-auto">
                    <span>
                        <a href="https://github.com/truongan/wecode-judge" target="_blank">&copy; Wecode Judge version ở đây nè</a>
                        <a href="https://github.com/truongan/wecode-judge/tree/docs" target="_blank">Docs</a>
                    </span><br/>
                    <small><span class="timer text-light"></span></small>
                </div>
            </ul>
            <ul class="navbar-nav sidenav-toggler bg-primary">
                <li class="nav-item">
                    <a class="nav-link text-center" id="sidenavToggler">
                    <i class="fa fa-fw fa-fw fa-angle-left"></i>
                    </a>
                </li>
            </ul>

            <div class="navbar-nav ml-auto p-3">
                <div class="top_object shj-spinner d-none">
                    <i class="fa fa-fw fa-refresh fa-spin fa-lg"></i>
                </div>
            </div>

            <div class="navbar-nav">
                <div class="top_object countdown d-flex flex-column justify-content-center" id="countdown">
                    <div class="time_block">
                        <span id="time_days" class="countdown_num">0 0 0 </span>
                    </div>
                </div>
                <div class="top_object countdown" id="extra_time">
                    <i class="fa fa-fw fa-plus-square fa-2x"></i>
                    <div class="time_block">
                        <span>Extra Time</span>
                    </div>
                </div>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item dropdown">
                <a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#" id="profile_link"><i class="fa fa-fw fa-user"></i>{{Auth::user()->username}}</a>
                    <div class="dropdown-menu dropdown-menu-right logout-menu">
                        <div class="d-flex pr-3 pl-3">
                            <div class="">
                                <div class="d-inline-flex">
                                    <form action="{{route('logout')}}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger mr-2 text-nowrap"><i class="fas fa-fw fa-sign-out-alt"></i>Sign out</button>
                                    </form>
                                    <a href="{{ route("users.edit", Auth::user()->id) }}" class="btn btn-info text-nowrap"><i class="fas fa-fw fa-wrench"></i>Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>