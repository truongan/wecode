@extends('layouts.bare')

@section('content')
<div class="row justify-content-center align-middle">
    <div class="box login">
    <h1 class="display-4 text-center ">{{ $settings['site_name']  }}</h1>
        {{-- <div class="jumbotron"> --}}
        {{-- </div> --}}
        <div class="login_form px-5">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="login1">
                    <p>
                        <label for="form_username">{{ __('User name') }}</label><br/>
                        <input id="username" type="username" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                        @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                        @enderror
                    </p>
                    <p>
                        <label for="form_password">{{ __('Password') }}</label><br/>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </p>
                    <p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                    </p>
                </div>
                <div class="login2">
                    <p style="margin:0;">
                        @if (Route::has('password.request'))
                            <a class="text-dark" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                        <input type="submit" value="Login" id="sharif_submit"/>
                    </p>
                </div>
            </form>
        </div>

    </div>
</div>
</body>

@endsection
