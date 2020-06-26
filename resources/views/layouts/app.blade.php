<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('head_title') - {{ $settings['site_name'] }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
	<link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Styles -->
	<link rel="stylesheet" type='text/css' href="{{ asset('assets/styles/bootstrap/' . $settings['theme']  . '.min.css') }}">
    <link rel="stylesheet" type='text/css' href="{{ asset('assets/sbadmin/css/sb-admin.css') }}">
    <link href="{{ asset('assets/styles/main.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fontawesome-free-5.3.1-web/css/all.css') }}" rel="stylesheet">
    @yield('other_assets')
</head>
<body id="body" class="fixed-nav ">
    @yield('mainnav', View::make('layouts.mainnav', ['selected' => $selected ?? '']))

    <div class="content-wrapper"><div class="container-fluid">
        <div class="row">
            <div id="page_title" class="jumbotron jumbontron-fluid py-3 mb-0 col-12 align-items-center d-flex">
                <i class="@yield('icon') fa-fw fa-lg"></i>
                <span>@yield('title')</span>
                @yield('title_menu')
            </div>
        </div>
        <div id="main_content" > 
            @yield('content')
        </div>
    
    </div>
    <script type="text/javascript" src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>


    <script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
    <script type='text/javascript' src="{{ asset('assets/sbadmin/js/sb-admin.min.js') }}"></script>

    <script	src="{{ asset('assets/js/notify.min.js') }}"></script>

    <script type="text/javascript">
        shj={};
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
        shj.offset = moment(shj_now_str).diff(moment());
        shj.time = moment();
        {{-- Thông số finish_time và extra_time cần được chỉnh sửa sau khi có assignment --}}
        
        shj.finish_time = moment("{!! (Auth::user()->selected_assignment->finish_time)?? now() !!}"); 
        shj.extra_time = moment.duration({!! (Auth::user()->selected_assignment->extra_time) ?? 0 !!}, 'seconds');
        shj.color_scheme = 'github';
    </script>

    
    <script type="text/javascript" src="{{ asset('assets/js/shj_functions.js') }}"></script>
    @yield('body_end')
</body>
</html>
