@extends('layouts.app')

@section('seo_title', __('main.cart'))
@section('meta_description', '')
@section('meta_keywords', '')

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    @if(!$cart->isEmpty())

        <section class="cart">
            <div class="container">
                <h1>{{ __('main.cart') }}</h1>
                <div class="content-top">
                    <strong>{{ __('main.products2') }}: <span class="cart_count">{{ $cart->getTotalQuantity() }}</span></strong>
                </div>
                <div class="row cart-wrap">
                    <div class="col-xl-9">
                        <div class="cart-items radius-14 cart_items_container">
                            @foreach($cartItems as $cartItem)
                                @php
                                    $product = $cartItem->associatedModel;
                                @endphp
                                <div class="cart-box cart_item_line ">
                                    <div class="cart-box__img">
                                        <a href="{{ $product->url }}">
                                            <img src="{{ $product->micro_img }}" alt="{{ $cartItem->name }}">
                                        </a>
                                    </div>
                                    <div class="cart-box__about">
                                        <h4>
                                            <a href="{{ $product->url }}" class="text-dark">{{ $cartItem->name }}</a>
                                        </h4>
                                    </div>
                                    <div class="cart-box__amount">
                                        <div>
                                            <div class="counter">
                                                <button type="button" class="radius-6" data-toggle="decrement"></button>
                                                <input type="text" class="update-cart-quantity-input" value="{{ $cartItem->quantity }}" name="cart-quantity-{{ $cartItem->id }}" data-id="{{ $cartItem->id }}" min="1" max="{{ $cartItem->availableQuantity }}" maxlength="3">
                                                <button type="button" class="radius-6" data-toggle="increment"></button>
                                            </div>
                                            <div class="{{ $cartItem->availableQuantity > 0 ? 'text-info' : 'text-danger' }}"><small>{{ __('main.available') }}: {{ $cartItem->availableQuantity }}</small></div>
                                        </div>

                                        <a href="{{ route('cart.delete', $cartItem->id) }}" class="remove-from-cart-btn" data-toggle="cart-box-delete">
                                            <svg width="16" height="16" fill="#666">
                                                <use xlink:href="#delete"></use>
                                            </svg>
                                            {{-- <span class="text-danger">&times;</span> --}}
                                        </a>
                                    </div>
                                    <div class="cart-box__price-d d-none d-lg-block text-nowrap">
                                        <b class="product_total">{{ Helper::formatPrice($cartItem->getPriceSumWithConditions()) }}</b>
                                    </div>
                                    <div class="cart-box__delete">
                                        <a href="{{ route('cart.delete', $cartItem->id) }}" class="remove-from-cart-btn" data-toggle="cart-box-delete">
                                            <svg width="16" height="16" fill="#666">
                                                <use xlink:href="#delete"></use>
                                            </svg>
                                            {{-- <span class="text-danger">&times;</span> --}}
                                        </a>
                                    </div>
                                    <div class="cart-box__price-m d-lg-none">
                                        <ul class="price-info__list">
                                            <li>
                                                <strong>{{ __('main.total') }}:</strong>
                                                <strong class="product_total">{{ Helper::formatPrice($cartItem->getPriceSumWithConditions()) }}</strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        @if ($address)
                        <div class="delivery-info radius-14 d-none d-lg-block">
                            <h3>{{ __('main.delivery') }}</h3>
                            <p>{{ __('main.delivery_address') }}: <span class="active_delivery_address">{{ $address->address_line_1 }}</span></p>
                            <a href="#edit-address-modal" data-toggle="modal">{{ __('main.to_edit2') }}</a>
                        </div>
                        @endif

                    </div>
                    <div class="col-xl-3">

                        <div class="total-info radius-14">
                            <div class="flex">
                                <h3>{{ __('main.total') }}</h3>
                                <h4>
                                    <span class="cart_total_price">{{ Helper::formatPrice($cart->getTotal()) }}</span>
                                </h4>
                            </div>
                            <ul class="total-info__list">
                                <li>
                                    <span>{{ __('main.products') }}</span>
                                    <span class="cart_standard_price_total">{{ Helper::formatPrice($standardPriceTotal) }}</span>
                                </li>
                                <li class="cart_discount_price_container @if($discount == 0) d-none @endif">
                                    <span>{{ __('main.discount') }}</span>
                                    <span class="text-danger">-<span class="cart_discount_price text-danger">{{ Helper::formatPrice($discount) }}</span></span>
                                </li>
                                {{-- <li>
                                    <span>{{ __('main.delivery') }}</span>
                                    <span>{{ __('main.free') }}</span>
                                </li> --}}
                            </ul>
                            <a href="{{ route('cart.checkout') }}" class="proceed-to-checkout-btn theme-btn radius-6 d-none d-xl-block">{{ __('main.proceed_to_checkout') }}</a>

                            {{-- Fixed button --}}
                            <div class="product-page-nav-bottom nav-bottom d-lg-none">
                                <div class="nav-bottom-buttons row">
                                    <div class="nav-bottom-buttons__item col flex-grow-1">
                                        <a href="{{ route('cart.checkout') }}" id="finish_order_btn"
                                                class="theme-btn radius-6"
                                                style="background-color: #F98329; font-size:16px; padding: 7px; display: block; width: 100%;"
                                        >
                                            </i> {{ __('main.proceed_to_checkout') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="cart-empty">
            <div class="container">
                <div class="cart-empty__wrap">
                    <img src="{{ asset('img/cart/cart-empty.png') }}" style="height: 180px;" alt="{{ __('main.cart_is_empty') }}">
                    <h1 class="text-center">{{ __('main.cart_is_empty') }}</h1>
                    <p class="text-center">
                        {!! __('main.cart_empty_description_1', ['url' => route('categories')]) !!}
                        @guest
                            {{ __('main.or') }}
                            {!! __('main.cart_empty_description_2', ['url' => route('login')]) !!} <br>
                            {!! __('main.cart_empty_description_3', ['url' => 'https://allgood/ref/from/allgood']) !!}

                            <a href="https://allgood/ref/from/allgood" class="btn mt-5 mx-auto"
                            style="
                            background-color: #F98329;
                            color:white!important;
                            font-size:16px;
                            padding: 7px;
                            display: block;
                            width: 200px;
                            font-weight: bolder;
                            "
                            class="text-white"
                            > <i class="bi bi-money"></i> {!! __('main.cart_empty_description_4') !!}</a>
                        @endguest
                    </p>
                </div>
            </div>
        </section>

        <x-bestseller-products></x-bestseller-products>

    @endif
</main>

@endsection


@section('after_footer_blocks')
<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-4">
                    {{ __('main.choose_address') }}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </h5>

                <div class="my-4">
                    @foreach ($addresses as $value)
                        <div class="custom-control custom-radio">
                            <input type="radio" id="address-line-{{ $value->id }}" value="{{ $value->id }}" name="address-line" class="custom-control-input" @if($address && $address->id == $value->id) checked @endif>
                            <label class="custom-control-label" for="address-line-{{ $value->id }}">{{ $value->address_line_1 }}</label>
                        </div>
                    @endforeach
                </div>
                <hr>
                <div class="my-4">
                    <h5>{{ __('main.add_address') }}</h5>
                    <a href="{{ route('addresses.create') }}" class="btn btn-sm btn-primary">{{ __('main.add') }}</a>
                </div>



            </div>
        </div>
    </div>
</div>
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

        $(function(){
            $('[name="address-line"]').on('change', function(){
                let addressID = $(this).val();
                let url = '{{ route('addresses.status.update', ['address' => 'address_id_placeholder', 'status' => 1]) }}';
                url = url.replace('address_id_placeholder', addressID);
                console.log(url);
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.address_line_1) {
                            $('.active_delivery_address').text(data.address_line_1);
                            $('#edit-address-modal').modal('hide');
                        }
                    });
            });
        });
    </script>
@endsection

@section('style')
    <style>
        .owl-carousel .owl-item {
            width: calc(50% - 10px);
            float: left;
        }
    </style>
@endsection
