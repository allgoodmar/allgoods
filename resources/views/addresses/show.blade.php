@extends('layouts.app')

@section('seo_title', __('main.profile'))

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <section class="profile">

        @include('partials.alerts')

        <div class="container">

            <h1 class="d-none d-lg-block">{{ __('main.profile') }}</h1>

            <div class="profile-user d-lg-none">
                <div class="profile-user__img">
                    <img src="{{ $user->avatar_img }}" alt="">
                </div>
                <div class="profile-user__content">
                    <h5>{{ $user->name }}</h5>
                </div>
            </div>
            <div class="row profile-wrap">
                <div class="col-6 profile-item__parent">
                    <div class="profile-item radius-10">
                        <div class="profile-item__img">
                            <svg width="40" height="40" fill="#005bff">
                                <use xlink:href="#cart"></use>
                            </svg>
                        </div>
                        <div class="profile-item__content">
                            <a href="{{ route('cart.index') }}" class="profile-item__title">{{ __('main.cart') }}</a>
                            <span class="d-none d-lg-inline-block">{{ __('main.products2') }}: <span class="cart_count">{{ $cartQuantity }}</span></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 profile-item__parent">
                    <div class="profile-item radius-10">
                        <div class="profile-item__img">
                            <svg width="40" height="40" fill="#005bff">
                                <use xlink:href="#heart"></use>
                            </svg>
                        </div>
                        <div class="profile-item__content">
                            <a href="{{ route('wishlist.index') }}" class="profile-item__title">{{ __('main.featured') }}</a>
                            <span class="d-none d-lg-inline-block">{{ __('main.products2') }}: <span class="wishlist_count">{{ $wishlistQuantity }}</span></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 profile-item__parent">
                    <div class="profile-item radius-10">
                        <div class="profile-item__img">
                            <svg width="40" height="40" fill="#005bff">
                                <use xlink:href="#messenger"></use>
                            </svg>
                        </div>
                        <div class="profile-item__content">
                            <a href="#" class="profile-item__title">{{ __('main.notifications') }}</a>
                            <span class="d-none d-lg-inline-block">@if($notifications > 0) {{ __('main.new_notifications') }}: {{ $notifications }} @else {{ __('main.no_new_notifications') }} @endif</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 profile-item__parent">
                    <div class="profile-item radius-10">
                        <div class="profile-item__img">
                            <img src="{{ $user->avatar_img }}" alt="{{ $user->name }}">
                        </div>
                        <div class="profile-item__content">
                            <a href="{{ route('profile.edit') }}" class="profile-item__title">{{ __('main.my_details') }}</a>
                            <span class="d-none d-lg-inline-block">{{ __('main.view_more') }}</span>
                        </div>
                        {{-- <a href="#" class="more-link d-none d-lg-inline-block">
                            <svg width="26" height="26" fill="#162e46">
                                <use xlink:href="#enter"></use>
                            </svg>
                        </a> --}}
                    </div>
                </div>
                {{-- <div class="col-6 profile-item__parent">
                    <div class="profile-item radius-10">
                        <div class="profile-item__img">
                            <svg class="d-none d-lg-inline-block" width="40" height="40" fill="#005bff">
                                <use xlink:href="#home"></use>
                            </svg>
                            <svg class="d-lg-none" width="40" height="40" fill="#005bff">
                                <use xlink:href="#delivery"></use>
                            </svg>
                        </div>
                        <div class="profile-item__content">
                            <a href="#" class="profile-item__title">Доставка</a>
                            <span class="d-none d-lg-inline-block">Адрес доставки</span>
                        </div>
                    </div>
                </div> --}}
                <div class="col-6 profile-item__parent">
                    <div class="profile-item radius-10">
                        <div class="profile-item__img">
                            <svg width="40" height="40" fill="#005bff">
                                <use xlink:href="#open-box"></use>
                            </svg>
                        </div>
                        <div class="profile-item__content">
                            <a href="{{ route('profile.orders') }}" class="profile-item__title">{{ __('main.my_orders') }}</a>
                            <span class="d-none d-lg-inline-block">{{ __('main.orders2') }}: {{ $ordersQuantity }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-4">
                <form class="logout-form" action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="btn btn-danger" type="submit">{{ __('main.nav.logout') }}</button>
                </form>
            </div>

        </div>
    </section>

    {{-- @include('partials.sidebar_profile') --}}

</main>

@endsection
