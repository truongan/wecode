<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>Wecode</title>


    <!-- Styles -->
    <link href="{{ asset('assets/styles/bootstrap/default.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" type='text/css' href="{{ url("assets/styles/login.css") }}"/>
    <link rel="icon" href="{{ url("assets/images/favicon.ico") }}"/>
</head>
<body id="body">
    <div id="app">
        <nav  class="navbar bg-dark navbar-expand-md navbar-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="https://www.uit.edu.vn/">
                    <img src="{{ asset('images/logo_uit.png') }}" height="60px"/>
                    <img src="{{ url('assets/images/banner.png') }}"/>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

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


<!-- Scripts -->
<script type="text/javascript" src="{{ asset('assets/js/jquery-3.6.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
