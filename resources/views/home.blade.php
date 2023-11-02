@extends('layouts.app')

@section('seo_title', "Смартфоны, электроника, одежда, продукты питания в Ташкенте и Узбекистане |онлайн рассрочка, купить, заказать в интернет магазине Allgood.uz")
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)
@section('body_class', 'home-page')

@section('content')

@if (session()->has('alert') || session()->has('success') || session()->has('status') || session()->has('error'))
    <div class="content-block">
        @include('partials.alerts')
    </div>
@endif

<main class="main">

    <x-week-products></x-week-products>

    <x-myslide></x-myslide> 

    {{-- <x-categories></x-categories> --}}

    <x-reviews></x-reviews>

    <x-bestseller-products></x-bestseller-products>

    <x-discounted-products></x-discounted-products>

    <section class="locker-b">
        <div class="container">
            <x-banner-line type="line_1"></x-banner-line>
            <div class="row locker-b__wrap">
                <div class="col-md-4 locker-b-box__parent">
                    <x-banner-middle type="middle_1"></x-banner-middle>
                </div>
                <div class="col-md-4 locker-b-box__parent">
                    <x-banner-middle type="middle_2"></x-banner-middle>
                </div>
                <div class="col-md-4 locker-b-box__parent">
                    <x-banner-middle type="middle_3"></x-banner-middle>
                </div>
            </div>
        </div>
    </section>

    @foreach ($homeCategoriesProducts as $homeCategoriesProductsBlock)
        @if (!$homeCategoriesProductsBlock['products']->isEmpty())
            <section class="products">
                <div class="container">
                    <div class="content-top">
                        <h2><a href="{{ $homeCategoriesProductsBlock['category']->url }}" class="text-dark">{{ $homeCategoriesProductsBlock['category']->name }}</a></h2>
                        <a href="{{ $homeCategoriesProductsBlock['category']->url }}" class="more-link" data-mobile-text="{{ __('main.all') }}">
                            <span>{{ __('main.view_all') }}</span>
                            <svg width="18" height="18" fill="#6b7279">
                                <use xlink:href="#arrow"></use>
                            </svg>
                        </a>
                    </div>
                    <div class="row products-wrap owl-carousel owl-theme">
                        @foreach ($homeCategoriesProductsBlock['products'] as $product)
                            <div class="product-card__parent col-12" style="margin-bottom: 18px!important;">
                                @include('partials.product_one')
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    <x-brands></x-brands>

    @if (!$articles->isEmpty())
        <section class="articles">
            <div class="container">
                <div class="content-top">
                    <h2>{{ __('main.articles') }}</h2>
                    <a href="{{ route('articles') }}" class="more-link" data-mobile-text="{{ __('main.all') }}">
                        <span>{{ __('main.all_articles') }}</span>
                        <svg width="18" height="18" fill="#6b7279">
                            <use xlink:href="#arrow"></use>
                        </svg>
                    </a>
                </div>
                <div class="row articles-wrap">

                    @foreach ($articles as $publication)
                        <div class="col-lg-3 col-6 article-item__parent">
                            @include('partials.publication_one')
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    @endif

    <section class="about">
        <div class="container">
            <div class="text-block customize_info">
                {!! $page->body !!}
            </div>
        </div>
    </section>

</main>

<!-- POP UP BANNER MODAL -->
@if (!empty($popupbanner))
    @if ($popupbanner->sale_price != 0)

    @php
        $price = $popupbanner->price;
        $sale_price = $popupbanner->sale_price;
        $percentage = 100 * ($price - $sale_price)/$price;
        $percentage = (int)$percentage;
    @endphp

        <div class="modal fade" id="pop-up-fast" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered col-md-5" role="document">
            <div class="modal-content left-main">
                <div class="modal-body" style="padding: 0px 12px!important;">

                    <div class="row">
                        <div class="col-md-4" style="
                        display: flex;
                        justify-content: center;
                        ">
                            <img src="{{ asset($popupbanner->img) }}" alt="" class="img-fluid" style="
                                text-align: center;
                                margin: auto;
                                display: block;
                            ">
                        </div>
                        <div class="col-md-8 d-flex justify-content-center left-nomain" id="mainDiv">
                            <div class="col-md-8 py-5 text-center">
                                <span class="text-muted">ТОЛЬКО СЕГОДНЯ!</span>
                                <br>
                                <b style="font-size: 34px;"> <span style="color: #F98329;">-{{ $percentage }}%</span> СКИДКА!</b>
                                <span class="d-block">на</span>
                                <b>{{ $popupbanner->name }}</b>
                                <br>

                                <span><del class="text-muted">{{ Helper::formatPrice($popupbanner->price) }}</del> </span>


                                <a href="{{ $popupbanner->url }}" class="btn mt-2" style="
                                    background-color: #F98329;
                                    color:#ffffff;
                                    font-size:16px;
                                    padding: 7px;
                                    display: block;
                                ">Купить за {{ Helper::formatPrice($popupbanner->sale_price) }}</a>
                            </div>
                            <a id="closeButton" class="text-white close-modal-button" data-dismiss="modal" aria-label="Close"></a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    @endif
@endif
<!-- POP UP BANNER MODAL ends -->

@endsection

@section('scripts')
    <script src="{{ asset('js/owl/owl.carousel.min.js') }}"></script>
    <script>

            $('.owl-carousel').owlCarousel({
                loop:true,
                center: false,
                autoplay:true,
                lazyLoad: true,
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

            $('#pop-up-fast').modal('show');

            setTimeout(function(){
                $('#pop-up-fast').modal('hide')
            }, 7000);

            setInterval(function(){
                $('#pop-up-light').fadeOut(1000).fadeIn(1000);
            }, 1000);
        // if user has already checked the confirmation button
        
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('js/owl/assets/owl.theme.default.min.css') }}">
    <style>
        .bottom {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #F98329;
            color: #ffffff;
            font-weight: bolder;
            font-size: 14px;
        }
    </style>
@endsection