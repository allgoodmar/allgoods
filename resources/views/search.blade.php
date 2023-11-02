@extends('layouts.app')

@section('seo_title', __('main.nav.search'))
@section('meta_description', '')
@section('meta_keywords', '')
@section('meta_robot', 'noindex, nofollow')

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <h1>{{ __('main.search_results') }}</h1>

        <form action="{{ route('search') }}" class="search-form">

            {{-- 
            <div class="input-group input-group-lg mb-4">
                <input type="text" name="q" class="form-control" placeholder="{{ __('main.search') }}" value="{{ $q }}">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">
                        {{ __('main.search') }}
                    </button>
                </div>
            </div>
            --}}

            @if(!$searches->isEmpty())

                <div class="row products-wrap">
                    @foreach ($searches as $product)
                        <div class="col-lg-20 col-12 product-card__parent">
                            @include('partials.product_one_second')
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {!! $links !!}
                </div>

            @else
                <div class="lead">
                    <div class="row">
                        <div class="col-md-6 text-center mx-auto">
                            <span class="d-block"><i class="bi bi-cloud-drizzle" style="font-size: 80px; color: #F98329;"></i></span>
                            {{ __('main.no_products') }}
                        </div>
                    </div>
                </div>
            @endif
        </form>

        <x-top-product></x-top-product>

    </div>
</main>

@endsection

@section('scripts')
    <script src="{{ asset('js/owl/owl.carousel.min.js') }}"></script>
    <script>
        $('.owl-carousel').owlCarousel({
            loop:true,
            center: false,
            autoplay:true,
            slideSpeed: 300,
            dots: false,
            responsive:{
                0:{
                    items:2
                },
                600:{
                    items:3
                },
                1000:{
                    items:6
                }
            }
        });
    </script>
@endsection
