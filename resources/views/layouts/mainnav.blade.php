<nav id="mainNav" class="mainNav navbar navbar-expand-lg navbar-dark fixed-top color-{{ $selected }} bg-dark">
        <a  class="navbar-brand" href="{{ url('/home') }}">
            <img src="{{ asset('images/logo.png') }}" height="30px" />
            {{ env('APP_NAME') }}
        </a>
    
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
        </button>
    
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav bg-dark navbar-sidenav color-{{ $selected }}" id="exampleAccordion">
                <li class="nav-item color-dashboard {{ ($selected=="dashboard") ? "selected" : ""}}">
                <a class="nav-link" href="{{ url('dashboard') }}">
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
                    <a class="nav-link" href="{{ url('submissions/') }}">
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

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link"  href="{{ url('assignments') }}">All assignments</a>
                </li>
                {{-- {%  if user.level >= 2 %} --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Tools
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ url('rejudge') }}">Rejudge</a>
                        <a class="dropdown-item" href="{{ url('queue') }}">Submission Queue</a>
                        {{-- <a class="dropdown-item" href="{{ url('moss/'~user.selected_assignment['id']) }}">Cheat Detection</a> --}}
                        <a class="dropdown-item" href="{{ url('htmleditor') }}">HTML editor</a>
                    </div>
                </li>
                {{-- {% endif %} --}}
                {{-- {# <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="{{ url('assignments') }}" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="assignment_name">{{ user.selected_assignment.name|length > 30 ? user.selected_assignment.name|slice(0, 30) ~ '...' : user.selected_assignment.name }}</span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        {% for assignment_item in all_assignments|reverse[:5] %}
                            <a class="dropdown-item select_assignment">
                                <i class="fa fa-fw fa-lg {{ assignment_item.id == user.selected_assignment.id ? 'fa-check-square-o color6' : 'fa-square-o' }}" data-id="{{ assignment_item.id }}"></i>
                                {{ assignment_item.name }}
                            </a>
                        {% endfor %}

                        <a  class="dropdown-item" href="{{ url('assignments') }}">All Assignments</a>

                    </div>
                </li> #} --}}
            </ul>


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
                    <a class="dropdown-toggle nav-link" data-toggle="dropdown" href="{{ url('profile') }}" id="profile_link"><i class="fa fa-fw fa-user"></i></a>
                    <div class="dropdown-menu dropdown-menu-right logout-menu">
                        <div class="d-flex pr-3 pl-3">
                            <div class="mr-3">
                                {{-- <div class="gravatar"><img src="https://www.gravatar.com/avatar/{{ md5(user.email) }}?s=70&d=identicon" /></div> --}}
                            </div>
                            <div class="">
                                <div class="name h4"><i class="fa fa-fw fa-user"></i>  user.username </div>
                                <div class="d-inline-flex">
                                    <a href="{{ url('logout') }}" class="btn btn-danger mr-2"><i class="fa fa-fw fa-sign-out"></i> Sign Out</a>
                                    <a href="{{ url('profile') }}" class="btn btn-info"><i class="fa fa-fw fa-wrench"></i> Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>