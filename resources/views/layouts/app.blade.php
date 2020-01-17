<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
	<link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Styles -->
	<link rel="stylesheet" type='text/css' href="{{ asset('css/bootstrap/default.min.css') }}">
    <link rel="stylesheet" type='text/css' href="{{ asset('css/sbadmin/css/sb-admin.css') }}">
    <link rel="stylesheet" type='text/css' href="{{ asset('css/fullcalendar/fullcalendar.min.css') }}">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome-free-5.3.1-web/css/all.css') }}" rel="stylesheet">
</head>
<body id="body" class="fixed-nav ">
    @yield('mainnav', View::make('layouts.mainnav', ['selected' => $selected]))

    <div class="content-wrapper"><div class="container-fluid">
        <div class="row">
            <div id="page_title" class="jumbotron jumbontron-fluid py-3 mb-0 col-12 align-items-center d-flex">
                <i class="@yield('icon') fa-fw fa-lg"></i>
                <span>@yield('title')</span>
                @yield('title_menu')
            </div>
        </div>
    
        <div id="main_content" class="row">
            @yield('content')
        </div>
    
    </div>
</body>
</html>
