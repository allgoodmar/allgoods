@extends('layouts.app')
@section('seo_title', __('main.verify_phone_number'))

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <form method="POST" action="{{ route('register.verify') }}">

                    @csrf

                    <input type="hidden" name="phone_number" value="{{ $phone_number }}">

                    <div class="form-group">
                        <label for="verify_code">{{ __('main.enter_verify_code') }}</label>
                        <input id="verify_code" type="text"
                                class="form-control @error('verify_code') is-invalid @enderror"
                                name="verify_code" required autofocus maxlength="6">

                        @error('verify_code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('main.form.to_confirm') }}
                    </button>
                </form>

            </div>
        </div>
    </div>

</main>


@endsection
