<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@yield('head_title') - {{ $settings['site_name'] }} - wecode judge</title>

	<!-- Fonts -->
	<link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

	<!-- Styles -->
	<link rel="stylesheet" type='text/css' href="{{ asset('assets/styles/bootstrap/' . $settings['theme']  . '.min.css') }}">
	<link rel="stylesheet" type='text/css' href="{{ asset('assets/bootstrap-icons-1.13.1/bootstrap-icons.min.css') }}">
	<link rel="stylesheet" type='text/css' href="{{ asset('assets/sbadmin/css/sb-admin.css') }}">
	<link href="{{ asset('assets/styles/main.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/fontawesome/css/all.min.css') }}" rel="stylesheet">
	@yield('other_assets')
</head>
<body id="body" class="fixed-nav ">
	@yield('mainnav', View::make('layouts.mainnav', ['selected' => $selected ?? '']))

	<div class="content-wrapper"><div class="container-fluid">
		<div class="row">
			<div id="page_title" class="fs-5 border shadow bg-light-subtle text-dark-subtle container-fluid py-3 mb-0 col-12 align-items-center d-flex">
				<i class="@yield('icon') fa-fw fa-lg"></i>
				<span>@yield('title')</span>
				@yield('title_menu')
			</div>
		</div>
		<div id="main_content" class="px-3 pt-3" >
			@yield('content')
		</div>
	</div>

	<script type="text/javascript" src="{{ asset('assets/js/jquery-3.6.3.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

	{{-- <script type='text/javascript' src="{{ asset('assets/sbadmin/js/sb-admin.min.js') }}"></script> --}}

	<script	src="{{ asset('assets/js/notify.min.js') }}"></script>

	<script type="text/javascript">
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		shj={};
		wcj={};
		wcj.site_url = site_url = "{{ URL::to('/') }}";
		var date = new Date(),
		utc = new Date(Date.UTC(
		  date.getFullYear(),
		  date.getMonth(),
		  date.getDate(),
		  date.getHours(),
		  date.getMinutes(),
		  date.getSeconds()
		));

		shj_now_str = utc.toLocaleTimeString();

		shj.offset = new Date("{{ date(DATE_ISO8601) }}") - new Date();
		shj.time = new Date();


		shj.finish_time = new Date("{!! (Auth::user()->selected_assignment->finish_time ?? now() )->format(DateTime::ISO8601) !!}");
		shj.extra_time = {!! (Auth::user()->selected_assignment->extra_time) ?? 0 !!};
		shj.color_scheme = 'github';



		/*!
		 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
		 * Copyright 2011-2025 The Bootstrap Authors
		 * Licensed under the Creative Commons Attribution 3.0 Unported License.
		 */

		(() => {
		  'use strict'

		  const getStoredTheme = () => localStorage.getItem('theme')
		  const setStoredTheme = theme => localStorage.setItem('theme', theme)

		  const getPreferredTheme = () => {
			const storedTheme = getStoredTheme()
			if (storedTheme) {
			  return storedTheme
			}

			return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
		  }

		  const setTheme = theme => {
			if (theme === 'auto') {
			  document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'))
			} else {
			  document.documentElement.setAttribute('data-bs-theme', theme)
			}
		  }

		  setTheme(getPreferredTheme())

		  const showActiveTheme = (theme, focus = false) => {
			const themeSwitcher = document.querySelector('#bd-theme')

			if (!themeSwitcher) {
			  return
			}

			const themeSwitcherText = document.querySelector('#bd-theme-text')
			const activeThemeIcon = document.querySelector('i.theme-icon-active')
			const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
			const svgOfActiveBtn = btnToActive.querySelector('i.bi').getAttribute('href')

			document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
			  element.classList.remove('active')
			  element.setAttribute('aria-pressed', 'false')
			})

			btnToActive.classList.add('active')
			btnToActive.setAttribute('aria-pressed', 'true')
			var a =  activeThemeIcon.classList

			let current = activeThemeIcon.classList[activeThemeIcon.classList.length -1]
			activeThemeIcon.classList.remove(current)
			activeThemeIcon.classList.add(`bi-${svgOfActiveBtn}`)
			console.log(a)
			// activeThemeIcon.classList = [...(a.slice(0,-1)), `bi-${svgOfActiveBtn}` ]
			// activeThemeIcon.setAttribute('href', svgOfActiveBtn)
			const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
			themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

			if (focus) {
			  themeSwitcher.focus()
			}
		  }

		  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
			const storedTheme = getStoredTheme()
			if (storedTheme !== 'light' && storedTheme !== 'dark') {
			  setTheme(getPreferredTheme())
			}
		  })

		  window.addEventListener('DOMContentLoaded', () => {
			showActiveTheme(getPreferredTheme())

			document.querySelectorAll('[data-bs-theme-value]')
			  .forEach(toggle => {
				toggle.addEventListener('click', () => {
				  const theme = toggle.getAttribute('data-bs-theme-value')
				  setStoredTheme(theme)
				  setTheme(theme)
				  showActiveTheme(theme, true)
				})
			  })
		  })
		})()


	</script>


	<script type="text/javascript" src="{{ asset('assets/js/shj_functions.js') }}"></script>
	@yield('body_end')
</body>
</html>
