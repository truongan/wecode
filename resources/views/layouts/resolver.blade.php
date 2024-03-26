<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('head_title') - {{ $settings['site_name'] }} - wecode judge</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
	<link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Styles -->
	<link rel="stylesheet" type='text/css' href="{{ asset('assets/styles/bootstrap/' . $settings['theme']  . '.min.css') }}">
    <link rel="stylesheet" type='text/css' href="{{ asset('assets/sbadmin/css/sb-admin.css') }}">
    <link href="{{ asset('assets/styles/main.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fontawesome/css/all.min.css') }}" rel="stylesheet">
    @yield('other_assets')
</head>
<body id="body">
    <div class="content-wrapper" style="margin: 0; margin-bottom: 3rem;"><div class="container-fluid">
        <div class="row">
            <div id="page_title" class="fs-5 border shadow bg-light text-dark container-fluid py-3 mb-0 col-12 align-items-center d-flex">
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
    {{-- <script type="text/javascript" src="{{ asset('assets/js/popper.min.js') }}"></script>  --}}
    {{-- Popper is included in bootstrap.bundle --}}
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
    </script>

    
    <script type="text/javascript" src="{{ asset('assets/js/shj_functions.js') }}"></script>
    @yield('body_end')
</body>
</html>
