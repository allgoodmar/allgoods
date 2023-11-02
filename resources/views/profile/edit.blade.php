@extends('layouts.app')
@section('seo_title', __('main.profile_edit'))

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    @include('partials.alerts')

    <div class="container py-4 py-lg-5">

        <div class="mb-5 d-none d-lg-block">
            <a href="{{ route('profile.show') }}">
                <strong> &larr; {{ __('main.profile') }}</strong>
            </a>
        </div>

        <div class="box mb-5">

            <h3 class="box-header">{{ __('main.profile_details') }}</h3>

            <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="form_name">{{ __('main.form.name') }} <span
                                    class="text-danger">*</span></label>
                            <input id="form_name" type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name') ?? $user->name }}" required
                                autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- <div class="form-group">
                            <label for="form_phone_number">{{ __('main.phone_number') }}</label>
                            <input id="form_phone_number" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') ?? $user->phone_number }}">
                            @error('phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> --}}

                        {{-- <div class="form-group">
                            <label for="form_address">{{ __('main.address') }}</label>
                            <textarea id="form_address"
                                    class="form-control @error('address') is-invalid @enderror"
                                    name="address"
                            >{{ old('address') ?? $user->address }}</textarea>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> --}}


                        <div class="form-group">
                            <label for="form_avatar">{{ __('main.image') }}</label>
                            @if ($user->avatar)
                                <div style="max-width: 100px;" class="mb-4">
                                    <img src="{{ $user->avatar_img }}" alt="{{ $user->name }}" class="img-fluid">
                                </div>
                            @endif
                            <input type="file" name="avatar" class="form-control">
                            @error('avatar')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                    </div>
                    <div class="col-lg-6">


                    </div>
                </div>


                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        {{ __('main.form.to_save') }}
                    </button>
                </div>

            </form>


        </div>

        <div class="box mb-5">
            <h3 class="box-header">{{ __('main.change_password') }}</h3>

            @if(Session::has('pmessage'))
                <div class="alert alert-success">
                    {{ Session::get('pmessage') }}
                </div>
            @endif

            <form action="{{ route('profile.password') }}" method="post">
                @csrf

                <div class="row">

                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="current_password">{{ __('main.form.current_password') }}
                                <span
                                    class="text-danger">*</span></label>
                            <input id="current_password" type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                name="current_password"
                                value="" required
                            >
                            @error('current_password')
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('main.form.new_password') }} <span
                                    class="text-danger">*</span></label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                value="" required
                            >
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label
                                for="password_confirmation">{{ __('main.form.confirm_password') }}
                                <span
                                    class="text-danger">*</span></label>
                            <input id="password_confirmation" type="password"
                                class="form-control"
                                name="password_confirmation"
                                value="" required
                            >
                        </div>

                    </div>
                </div>


                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        {{ __('main.form.to_save') }}
                    </button>
                </div>

            </form>


        </div>

    </div>

</main>

<div class="section">
</div>


@endsection
