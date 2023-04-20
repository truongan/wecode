<nav id="mainNav" class="mainNav navbar navbar-expand-lg navbar-dark fixed-top color-{{ $selected }} bg-dark ">

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
    </button>

    <a  class="navbar-brand" href="{{ route('home') }}" class="link-none"><img src="{{ asset('images/logo.png') }}" height="30px" />{{ $settings['site_name'] }}</a>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav navbar p-0 border-bottom-0 border-start-0 border-end-0 bg-dark navbar-dark navbar-sidenav color-{{ $selected }} align-items-start" id="exampleAccordion">
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
            <li class="nav-item color-problem_list {{ ($selected=="problem_list") ? "selected" : ""}}">
                <a class="nav-link" href="{{ route('lops.index') }}">
                    <i class="fas fa-school fa-fw fa-lg"></i>
                    <span class="nav-link-text">Classes</span>
                </a>
            </li>
            <li class="nav-item color-users {{ ($selected=="users") ? "selected" : ""}}">
                <a href="{{route('practice')}}" class="nav-link">
                    <i class="fas fa-khanda fa-fw fa-lg"></i>
                    <span class="nav-link-text">Practice</span>
                </a>
            </li>
            
            @if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
                <li class="nav-item color-instructor_panel {{ ($selected=="instructor_panel") ? "selected" : ""}}" >
                    <a class="nav-link" href="{{ route('problems.index') }}">
                        <i class="fa fa-fw fa-sliders-h fa-lg"></i>
                        <span class="nav-link-text">Problems</span>
                    </a>
                </li>
            @endif

            <li class="nav-item color-assignments {{ ($selected=="assignments") ? "selected" : ""}}">
                <a class="nav-link" href="{{ url('assignments') }}">
                    <i class="fa fa-fw fa-folder-open fa-lg"></i>
                    <span class="nav-link-text">Assignments</span>
                </a>
            </li>
            <li class="nav-item color-all_submissions {{ ($selected=="all_submissions") ? "selected" : ""}}">
                @if ( in_array( Auth::user()->role->name, ['student', 'guest']) ) 
                    <a class="nav-link" href="{{ route('submissions.index', [(int)Auth::user()->selected_assignment_id, Auth::user()->id, 'all', 'all'])}}">
                @else
                    <a class="nav-link" href="{{ route('submissions.index', [(int)Auth::user()->selected_assignment_id, 'all', 'all', 'all'])}}">
                @endif
                    <i class="fa fa-fw fa-bars fa-lg"></i>
                    <span class="nav-link-text">Submissions</span>
                </a>
            </li>
            <li class="nav-item color-scoreboard {{ ($selected=="scoreboard") ? "selected" : ""}}">
                <a class="nav-link" 
                @if (isset(Auth::user()->selected_assignment_id))
                href="{{ route('scoreboards.index', (int)Auth::user()->selected_assignment_id) }}"
                @else
                href="{{ route('scoreboards.index', 0 )}}"
                @endif

                >
                    <i class="fa fa-fw fa-star fa-lg"></i>
                    <span class="nav-link-text">Scoreboard</span>
                </a>
            </li>

            <li class="nav-item color-freeze {{ ($selected=="freeze") ? "selected" : ""}}">
                <a class="nav-link" 
                @if (isset(Auth::user()->selected_assignment_id))
                href="{{ route('scoreboards.freeze', (int)Auth::user()->selected_assignment_id) }}"
                @else
                href="{{ route('scoreboards.freeze', 0 )}}"
                @endif
                >
                    <i class="fa fa-fw fa-snowflake fa-spin fa-lg"></i>
                    <span class="nav-link-text">Scoreboard freeze</span>
                </a>
            </li>

            <div class="p-1 sidenav-bottom nav-item mt-auto">
                <a target="_blank" href="https://www.uit.edu.vn/"><img src="{{ asset('images/logo_uit.png') }}" height="20px" /></a>
                <a target="_blank" href="https://cs.uit.edu.vn/"><img src="{{ asset('images/logo_cs.png') }}" height="20px"/></a>
                <a href="https://github.com/truongan/wecode-judge" target="_blank">&copy; Wecode Judge</a>
                <a href="https://github.com/truongan/wecode-judge/tree/docs" target="_blank">Docs</a>
                <br/>
                <small><span class="timer text-light"></span></small>
            </div>
        </ul>

        <div class="navbar-nav ms-auto">
            @if(isset(Auth::user()->selected_assignment->name))
                <div class="px-3 d-flex flex-column justify-content-center  bg-secondary bg-opacity-75" >
                        {{Auth::user()->selected_assignment->name}}  
                </div>
            @endif
            
            <div class="top_object countdown d-flex flex-column justify-content-center" id="countdown">
                <div class="time_block">
                    <span id="time_days" class="countdown_num"></span>
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
                <a class="dropdown-toggle nav-link" data-bs-toggle="dropdown" href="#" id="profile_link" rol="button" aria-expandd="false"><i class="fa fa-fw fa-user"></i>{{Auth::user()->username}}</a>
                <div class="dropdown-menu dropdown-menu-end ">
                    <div class="d-flex pe-3 ps-3">
                        <div class="">
                            <div class="d-inline-flex">
                                <form action="{{route('logout')}}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger me-2 text-nowrap"><i class="fas fa-fw fa-sign-out-alt"></i>Sign out</button>
                                </form>
                                <a href="{{ route("users.show", Auth::user()->id) }}" class="btn btn-info text-nowrap"><i class="fas fa-fw fa-wrench"></i>Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>