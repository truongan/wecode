<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>Wecode</title>

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('assets/styles/bootstrap/default.min.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ url("assets/images/favicon.ico") }}"/>
	<link rel="stylesheet" type='text/css' href="{{ url("assets/styles/login.css") }}"/>
</head>
<body id="body">
    <div id="app">
        <nav style="background-color: rgb(46, 46, 46)" class="navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="https://www.uit.edu.vn/">
                    <img src="{{ asset('images/logo_uit.png') }}" height="60px"/>
                </a>
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ url('assets/images/banner.png') }}"/>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->

                            @if ( Route::currentRouteName() == 'register'  )
                            <li class="nav-item">
                                <a class="nav-link text-light" href="{{ route('login') }}">{{ __('login') }}</a>
                            </li>
                            @endif
                            @if ( Route::currentRouteName() != 'register' && $settings['enable_registration']  )
                            <li class="nav-item">
                                <a class="nav-link text-light" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                            @endif

                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
