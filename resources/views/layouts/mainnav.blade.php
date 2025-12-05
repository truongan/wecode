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

			@if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
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
			<svg class="text-body" xmlns="http://www.w3.org/2000/svg" style="display: none;">
			  <symbol id="circle-half" viewBox="0 0 16 16">
				<path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
			  </symbol>
			  <symbol id="moon-stars-fill" viewBox="0 0 16 16">
				<path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
				<path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
			  </symbol>
			  <symbol id="sun-fill" viewBox="0 0 16 16">
				<path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
			  </symbol>
			</svg>

			<button class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center"
					id="bd-theme"
					type="button"
					aria-expanded="false"
					data-bs-toggle="dropdown"
					data-bs-display="static"
					aria-label="Toggle theme (auto)">
			  {{-- <svg width="1em" height="1em" width="1em" class="text-success bi my-1 theme-icon-active"><use href="#circle-half"></use></svg> --}}
				<i class="theme-icon-active bi bi-circle-half"></i>
			  <span class="d-lg-none ms-2" id="bd-theme-text">Toggle theme</span>
			</button>
			<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
			  <li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
				  {{-- <svg width="1em" height="1em" class="text-success bi bi-sun-fill me-2 opacity-50 theme-icon"><use href="#sun-fill"></use></svg> --}}
					<i class="bi bi-sun-fill" href="sun-fill"></i>
				  Light

				</button>
			  </li>
			  <li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
				  {{-- <svg width="1em" height="1em" class="text-success bi me-2 opacity-50 theme-icon"><use href="#moon-stars-fill"></use></svg> --}}
					<i class="bi bi-moon-stars-fill" href="moon-stars-fill"></i>
				  Dark

				</button>
			  </li>
			  <li>
				<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
				  {{-- <svg width="1em" height="1em" class="text-success bi me-2 opacity-50 theme-icon"><use href="#circle-half"></use></svg> --}}
					<i class="bi bi-circle-half" href="circle-half"></i>
				  Auto

				</button>
			  </li>
			</ul>
		  </li>

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
