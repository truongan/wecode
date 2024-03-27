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
    <div class="content-wrapper" style="margin: 0; margin-bottom: 3rem; overflow: visible;"><div class="container-fluid">
        <div class="row">
            <div id="page_title" class="fs-5 border bg-light text-dark container-fluid py-3 mb-0 col-12 align-items-center d-flex justify-content-between">
                <div class="d-flex">
                    <div>
                        <i class="@yield('icon') fa-fw fa-lg"></i>
                        <span style="margin-right: 24px;">@yield('title')</span>
                    </div>
                    @yield('title_menu')
                </div>
                <div>
                    @yield('contest_time')
                </div>
            </div>
        </div>
        <div id="main_content" class="px-3 pt-3" >
            <div class="mx-n2" style="overflow: visible">
                <div class="d-flex justify-content-center mb-5">
                    <img style="width: 100%; border-radius: 4px;" src="{{ url('images/wecode_challenge2024/Cover@4x.png') }}" alt="contest cover">
                </div>
                @yield('content')
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="{{ asset('assets/js/jquery-3.6.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    @yield('body_end')
</body>
</html>
