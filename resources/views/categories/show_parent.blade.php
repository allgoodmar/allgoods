@extends('layouts.app')

@section('seo_title', $category->seo_title ?: $category->name)
@section('meta_description', $category->meta_description)
@section('meta_keywords', $category->meta_keywords)
@section('body_class', 'parent-category-page')

@section('content')

@php
    $siteLogo = setting('site.logo');
    $logo = $siteLogo ? Voyager::image($siteLogo) : '/img/logo.png';
    $siteTitle = setting('site.title')
@endphp

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <section class="hero-banner">
        <div class="container">
            <h1>{{ $category->name }}</h1>
            <div class="content-top align-items-center d-none d-lg-block">
                <strong>{{ $category->description }}</strong>
            </div>
            @if (!$subcategories->isEmpty())
                <nav class="navbar-d radius-6 d-none d-lg-block">
                    <ul class="navbar-d__list">
                        @foreach ($subcategories as $subcategory)
                            <li class="navbar-d__item">
                                <a href="{{ $subcategory->url }}" class="navbar-d__link">{{ $subcategory->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
                <nav class="navbar-m d-lg-none">
                    <ul class="navbar-m__list">
                        <li class="navbar-m__item">
                            <a href="#" class="navbar-m__link" data-toggle-menu="category-menu">
                                <svg width="30" height="30" stroke="#1a2c3c">
                                    <use xlink:href="#menu"></use>
                                </svg>
                                <span>{{ __('main.categories') }}</span>
                                <svg width="18" height="18" fill="#999">
                                    <use xlink:href="#arrow"></use>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </nav>
            @endif



            {{-- 
            <div class="hero-banner-swiper swiper-container">
                <div class="swiper-wrapper">
                    @foreach ($slides as $slide)
                        <div class="swiper-slide">
                            <div class="hero-banner-swiper__item">
                                <div class="hero-banner-swiper__item-about">
                                    <h2>{{ $slide->name }}</h2>
                                    <p class="sub-title">{{ $slide->description }}</p>
                                    @if ($slide->button_text && $slide->url)
                                        <a href="{{ $slide->url }}" class="theme-btn radius-6">{{ $slide->button_text }}</a>
                                    @endif
                                </div>
                                <div class="hero-banner-swiper__item-img">
                                    <img src="{{ $slide->img }}" alt="{{ $slide->name }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="swiper-pagination"></div>
            </div>
            --}}
        </div>
    </section>

    @if (!$subcategories->isEmpty())
    <section class="sub-categories">
        <div class="container">
            <div class="content-top">
                <h2>{{ __('main.subcategories') }}</h2>
            </div>
            <div class="row sub-categories-wrap">
                @foreach ($subcategories as $subcategory)
                    <div class="col-lg-2 col-4 sub-categories-box__parent">
                        @include('partials.category_one', ['category' => $subcategory])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <x-bestseller-products></x-bestseller-products>

    <x-discounted-products></x-discounted-products>

    <x-brands></x-brands>

    <section class="about">
        <div class="container">
            <div class="text-block">
                {!! $category->body !!}
            </div>
        </div>
    </section>

</main>

@if (!$subcategories->isEmpty())
    <div class="category-menu" data-target-menu="category-menu">
        <div class="category-menu__header">
            <button type="button" data-toggle="menu-close">
                <svg width="24" height="24" fill="#333">
                    <use xlink:href="#close"></use>
                </svg>
            </button>
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ $logo }}" alt="{{ $siteTitle }}" class="img-fluid">
                </a>
            </div>
        </div>
        <div class="category-menu__content">
            <div class="category-menu__body">
                <ul class="category-menu__list">
                    @foreach ($subcategories as $subcategory)
                        <li>
                            <a href="{{ $subcategory->url }}" class="text-uppercase">
                                <span>{{ $subcategory->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
    <script src="{{ asset('js/owl/owl.carousel.min.js') }}"></script>
    <script>
        $('.owl-carousel').owlCarousel({
            items: 6,
            margin: 20,
            loop:true,
            center: false,
            autoplay:true,
            slideSpeed: 300,
            dots: true,
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
