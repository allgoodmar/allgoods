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

            <div class="row">
                <div class="col-6">
                    <h1 class="d-none d-lg-block">{{ __('main.profile') }}</h1>

                    <div class="profile-user d-lg-none">
                        <div class="profile-user__img">
                            <img src="{{ $user->avatar_img }}" alt="">
                        </div>
                        <div class="profile-user__content">
                            <h5>{{ $user->name }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    @if (!empty(auth()->user()->coupon_sum))
                        @if (auth()->user()->is_coupon_used == 'no')
                            <p class="text-right mt-4"> <img src="{{ asset('img/coupon2.svg') }}" style="color: yellowgreen;" width="24px;" height="24px;"> <b class="text-dark"> {{ __('main.coupon') }}: </b> <span style="color: green;">{{ Helper::formatPrice(auth()->user()->coupon_sum) }}</span>  </p>
                        @endif
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="label">
                        {!! __("main.ref1") !!}
                    </div>
                    <div class="copy-text input-group">
                        <input type="text" class="text form-control" style="width: 45%;" placeholder="Здесь будет ваша реферальная ссылка" value="@if(!empty($copyUrl)) {{ $copyUrl }} @endif"/>
                        <div class="input-group-append">
                            <button style="background-color: #F98329;" class="btn text-white">Копировать</button>
                        </div>
                    </div>
                    <div class="label text-muted">
                        {!! __("main.ref2") !!} <span class="badge @if(isset($countRefs) && $countRefs == 0) bg-danger @else bg-success @endif  text-white">
                            @if(!empty($countRefs))
                                {{ $countRefs }}
                            @endif
                        </span>
                    </div>
                    @if(isset($countRefs) && $countRefs == 0)
                    <div class="label text-muted">
                        {!! __("main.ref3") !!}
                    </div>
                    @else 
                    <div class="label text-muted">
                        <span class="text-dark" style="font-weight: bolder;">{{ __("main.ref4") }}</span>
                    </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <button class="btn btn-primary mt-4 w-100" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        {{ __("main.ref5") }}
                    </button>
                    <div class="collapse" id="collapseExample">
                        <div class="card card-body">
                            {{ __("main.ref6") }}
                            <br>
                            <span class="text-muted mt-1">{{ __("main.ref7") }}</span>
                            <a href="https://t.me/myallgood_bot" target="_blank" class="text-dark"> <i class="bi bi-telegram" style="color: #26A5E4;"></i>{{ __("main.ref8") }}</a>
                        </div>
                      </div>
                </div>
            </div>

            <hr>
            
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
                            <a href="{{ route('profile.documents.index') }}" class="profile-item__title">{{ __('main.moiDokumenti') }}</a>
                            @if ($process < 3)
                                <span class="text-danger">Не заполнено {{ $process }}/4</span>
                            @else
                                <span class="text-success">Заполнено {{ $process }}/4</span>
                            @endif
                            {{-- <span class="d-none d-lg-inline-block">To'ldirilmagan</span> --}}
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
                <div class="col-6 profile-item__parent">
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
                            <a href="{{ route('addresses.index') }}" class="profile-item__title">{{ __('main.addresses') }}</a>
                            <span class="d-none d-lg-inline-block">{{ __('main.delivery_address') }}</span>
                        </div>
                    </div>
                </div>
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

@section('scripts')
    <script>
        let copyText = document.querySelector(".copy-text");
        copyText.querySelector("button").addEventListener("click", function () {
            let input = copyText.querySelector("input.text");
            input.select();
            document.execCommand("copy");
            copyText.classList.add("active");
            window.getSelection().removeAllRanges();
            setTimeout(function () {
                copyText.classList.remove("active");
            }, 2500);
        });

    </script>
@endsection