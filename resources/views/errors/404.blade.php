@extends('layouts.app')
@section('seo_title', __('main.page_not_found'))

@section('content')

<main class="main">

    <div class="container py-4 py-lg-5">

        @php
            $page404Text = Helper::staticText('404_page', 30);
        @endphp

        <div class="my-5 py-5">
            <div class="row align-items-center">
                <div class="col-sm-6 mb-5 mb-sm-0">
                    <div class="mb-4">
                        <img src="{{ asset('images/404.png') }}" alt="404" class="img-fluid" style="width: 200px;">
                    </div>
                    <h1>{{ $page404Text->name }}</h1>
                    <p>{{ $page404Text->description }}</p>
                </div>
                <div class="col-sm-6">
                    <img src="{{ $page404Text->img }}" alt="404" class="img-fluid" style="width: 400px;">
                </div>
            </div>
        </div>

        <x-categories></x-categories>

        {{-- <div class="text-center">
            <br>
            <br>

            <br>
            <br>

            <h1>404 - {{ __('main.page_not_found') }}</h1>

            <div class="mb-4"><a href="{{ route('home') }}">{{ __('main.go_home_page') }}</a></div>

            <div class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-md-4 col-sm-6">
                        <form action="{{ route('search') }}">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control bg-white border-secondary">
                                <span class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">{{ __('main.search') }}</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <br>
            <br>

            <br>
            <br>
        </div> --}}

    </div>

</main>

@endsection

@section('scripts')
    <script>window.location.replace("{{ route('home') }}");</script>
@endsection