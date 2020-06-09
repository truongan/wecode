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
    <script src="{{ asset('js/app.js') }}" defer></script>

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
        <nav style="background-color: rgb(0, 0, 0)" class="navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @if (Route::has('login'))
                                @auth
                                    <a class="nav-link text-light" href="{{ url('/home') }}">Home</a>
                                @else
                                    <a class="nav-link text-light" href="{{ route('login') }}">Login</a>

                                    @if (Route::has('register'))
                                        <a class="nav-link text-light" href="{{ route('register') }}">Register</a>
                                    @endif
                                @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <img src="{{ url('assets/images/wecode.png') }}" width="100%"/>
        </main>
    </div>
</body>
</html>
