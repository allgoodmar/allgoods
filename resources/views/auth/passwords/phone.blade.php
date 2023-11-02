@extends('layouts.app')
@section('seo_title', __('main.nav.reset_password'))

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <h1 class="text-center">{{ __('main.nav.reset_password') }}</h1>

        <div class="row justify-content-center">
            <div class="col-lg-6">

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.phone') }}">
                    @csrf

                    <div class="form-group">
                        <label for="phone_number">
                            <span>{{ __('main.phone_number') }}</span>
                        </label>

                        <input id="phone_number" type="text" class="phone-input-mask form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" autofocus pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">

                        @error('phone_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('main.form.send') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

@endsection
