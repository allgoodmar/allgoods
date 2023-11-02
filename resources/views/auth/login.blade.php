@extends('layouts.app')
@section('seo_title', __('main.nav.login'))
@section('meta_robot', 'noindex, nofollow')

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <div class="row">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <div class="login_part_form">

                    <h3>{{ __('main.login_to_account') }}</h3>

                    <form action="{{ route('login') }}" method="POST" >
                        @csrf

                        {{-- <div class="form-group">
                            <label for="email">{{ __('main.email') }}</label>
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autofocus required>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> --}}

                        <div class="form-group">
                            <label for="phone_number">{{ __('main.phone_number') }} <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control phone-input-mask @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                            @error('phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('main.password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('main.remember_me') }}?
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                {{ __('main.to_login') }}
                            </button>
                            <a class="btn-link ml-2" href="{{ route('password.phone') }}">
                                {{ __('main.nav.forgot_password') }}
                            </a>
                        </div>

                    </form>

                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="w-100 h-100">
                    <div class="h-100 p-4 bg-light border-radius text-center d-flex justify-content-center align-items-center flex-column">
                        <h2>{{ __('main.dont_have_an_account') }}?</h2>
                        <a class="btn btn-lg btn-primary" href="{{ route('register') }}">{{ __('main.nav.register') }}</a>
                    </div>
                </div>
            </div>
        </div>



    </div>

</main>



@endsection
