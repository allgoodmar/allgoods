@php
    if (!isset($size)) {
        $size = 'small';
    }
    $showImg = $size . '_img';
    $showSecondImg = 'second_' . $size . '_img';
@endphp

<div class="product-card large">
    <div class="product-card__wrap">
        <div class="product-card__header">

            @if (!empty($product->brand->id) && !empty($categoryBrands))
                @foreach ($categoryBrands as $key2 => $value2)
                    @if ($product->brand->id == $value2->id)
                        @if ($value2->slug == 'samsung')
                            <div style="margin-bottom: 4px;">
                                <span class="samsung_header_logo" style="width: 440px!important; height: 40px!important; color:#13279B; font-weight: bolder; margin-bottom: 200px; padding-top: 100px!imporant; padding-bottom: 100px!imporant;">
                                    <span style="width: 100%!important; height: 40px!important; border-radius: 20px; padding-top: 100px!imporant; padding-bottom: 100px!imporant;">
                                        SAMSUNG
                                    </span>
                                </span>
                            </div>
                        @else
                            <div>
                                <span class="status" style="width: 140px; height: 30px;">
                                    <img src="{{ $value2->small_img }}" style="width: 100%; height: 30px; border-radius: 20px;" alt="brand_image_of_{{ $value2->name }}">
                                </span>
                            </div>
                        @endif
                    @endif
                @endforeach
            @endif

            <a href="javascript:;"
                class="favorite-link d-none d-lg-flex @if(!app('wishlist')->get($product->id)) add-to-wishlist-btn @else remove-from-wishlist-btn active @endif only-icon"
                data-id="{{ $product->id }}"
                data-add-url="{{ route('wishlist.add') }}"
                data-remove-url="{{ route('wishlist.delete', $product->id) }}"
                data-name="{{ $product->name }}"
                data-price="{{ $product->current_price }}"
                data-add-text="<svg width='28' height='28' fill='#005bff'><use xlink:href='#heart'></use></svg>"
                data-delete-text="<svg width='28' height='28' fill='#005bff'><use xlink:href='#heart'></use></svg>"
            >
                <svg width="24" height="24" fill="#0b2031"><use xlink:href="#heart"></use></svg>
            </a>
            <a href="{{ $product->url }}" class="d-none d-lg-inline-block" >
                <img src="{{ $product->$showImg }}" alt="{{ $product->name }}" class="img-fluid">

                <div class="status-list">
                    @if ($product->getModel()->isBestseller())
                        <span class="status green">Top</span>
                    @endif
                    @if ($product->getModel()->isPromotion())
                        <span class="status purple">{{ __('main.promotion') }}</span>
                    @endif
                    @if ($product->getModel()->isDiscounted())
                        <span class="status red">-{{ $product->getModel()->discount_percent }}%</span>
                        <span class="badge bg-success countdown-sale text-white"><i class="bi bi-clock"></i> {{ __('main.hurry_up') }}</span>
                    @endif
                    @if (!$product->getModel()->isAvailable())
                        <span class="status gray rounded">{{ __('main.not_in_stock') }}</span>
                    @endif
                    @if ($product->getModel()->isRadiocom())
                        <span class="status isRadiocom rounded border border-danger" style="background-color: white; color: black;"><i style="color: red;"><i style="color: black;">by</i> RADIOCOM </i> </span>
                    @endif
                </div>
            </a>
            <div class="product-card-swiper swiper-container d-lg-none">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="product-card-swiper__item">
                            <a href="{{ $product->url }}" class="d-block">
                                @if(!$product->installmentPlans->isEmpty())
                                    <strong class="bg-yellow installment-text radius-20">{{ __('main.installment_payment') }} 0-0-{{ $product->max_months }}</strong>
                                @endif
                                <img src="{{ $product->small_img }}" alt="{{ $product->name }}" class="img-fluid">
                            </a>
                        </div>
                    </div>
                    @foreach ($product->small_imgs as $key => $smallImg)
                        @if ($key == 2)
                            @break
                        @endif
                        <div class="swiper-slide">
                            <div class="product-card-swiper__item">
                                <a href="{{ $product->url }}" class="d-block">
                                    <img src="{{ $smallImg }}" alt="{{ $product->name . ' ' . $key }}" class="img-fluid">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="product-one-rating d-lg-none">
                @php
                    $rating = $product->rating_avg;
                    if ($rating == 0) {
                        $rating = 5;
                    }
                @endphp
                @include('partials.stars', ['rating' => $rating])
            </div>
            <span class="d-lg-none">{{ __('main.reviews2') }}: {{ $product->rating_count }}</span>
            {{-- <div class="product-card-stickers d-none d-lg-block">
                @if (!$product->getModel()->isAvailable())
                    <span class="d-inline-block px-2 py-1 small bg-light text-dark rounded">{{ __('main.not_in_stock') }}</span>
                @endif
            </div> --}}
            <div class="status-list d-none d-lg-block">
                @if (!$product->getModel()->isAvailable() && $product->getModel()->getProductStatus() == \App\Product::STATUS_INACTIVE)
                    <span class="status gray rounded">{{ __('main.not_in_stock') }}</span>
                @elseif ($product->getModel()->isAvailable() && $product->getModel()->getProductStatus() == \App\Product::STATUS_SOON)
                    <span class="status gray rounded">{{ __('main.soon_in_stock') }}</span>
                @endif
            </div>
        </div>
        <div class="product-card__content">
            <div class="product-card__top d-lg-none">
                @if($product->getModel()->isDiscounted())
                    <strong class="bg-danger radius-20 sale-text">-{{ $product->getModel()->discount_percent }}%</strong>
                @endif
                <b>
                    <svg width="24" height="24">
                        <use xlink:href="#confirmation"></use>
                    </svg>
                    <span>
                        @if ($product->getModel()->isAvailable() && $product->getModel()->getProductStatus() == \App\Product::STATUS_ACTIVE)
                            {{ __('main.in_stock') }} 
                        @elseif ($product->getModel()->isAvailable() && $product->getModel()->getProductStatus() == \App\Product::STATUS_SOON)
                            {{ __('main.soon_in_stock') }} 
                        @else 
                            {{ __('main.not_in_stock') }} 
                        @endif</span>
                </b>
            </div>
            <div class="product-card__body">
                <a href="{{ $product->url }}" class="d-block">

                    <div class="flex ml-0 pl-0">
                        {{-- 
                        <strong class="bg-orange text-white text-nowrap radius-4">{{ __('main.price_per_month', ['price' => Helper::formatPrice($product->min_price_per_month)]) }}</strong>
                        --}}
                        <strong class="ml-0 pl-0">{{ __('main.price_per_month', ['price' => Helper::formatPrice($product->min_price_per_month)]) }}</strong>
                    </div>
                    <p class="text-price text-nowrap">{{ Helper::formatPrice($product->current_price) }}</p>
                    {{-- @if($product->getModel()->isDiscounted())
                        <del class="old-price text-nowrap">
                            {{ Helper::formatPrice($product->price) }}
                        </del>
                    @else
                        <span class="old-price">
                            &nbsp;
                        </span>
                    @endif --}}
                    <span class="title-link">
                        {{ $product->name }}
                    </span>

                    <div class="product-one-rating d-none d-lg-flex">
                        @php
                            $rating = $product->rating_avg;
                            if ($rating == 0) {
                                $rating = 5;
                            }
                        @endphp
                        @include('partials.stars', ['rating' => $rating])
                    </div>

                    <div class="delivery-info radius-6 d-lg-none">
                        <b>{{ __('main.product_one_text_2') }}</b>
                        <p>{{ __('main.product_one_text_1') }}</p>
                    </div>
                </a>
                {{-- <ul class="characteristics-list d-lg-none">
                    <li>
                        <span>Диагональ экрана:</span>
                        <b>6.5</b>
                    </li>
                    <li>
                        <span>Оперативная память:</span>
                        <b>4 Gb</b>
                    </li>
                </ul> --}}
            </div>
            <div class="product-card__footer">

                <div class="btn-items">
                    <div class="btn-item">
                        <a href="javascript:;"
                            class="favorite-link icon-btn radius-6 only-icon add-to-cart-btn @if (!$product->getModel()->isAvailable()) disabled @endif "
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->current_price }}"
                            data-fixPrice="{{ $product->fix_price }}"
                            data-quantity="1"
                            data-in-stock="{{ $product->getModel()->getStock() }}"
                            id="add_to_cart_analytics"
                            data-add-text="<svg width='28' height='28' fill='#005bff'><use xlink:href='#cart'></use></svg>"
                            data-delete-text="<svg width='28' height='28' fill='#005bff'><use xlink:href='#cart'></use></svg>"
                            style="height:38px!important;
                            width:38px!important;
                            background-color: #e7e8ea;
                            display: flex;
                            justify-content: center;
                            align-items: center;"
                        >
                        <svg width="28" height="28" fill="#005bff"><use xlink:href="#cart"></use></svg>
                        </a>
                    </div>
                    <div class="btn-item flex-grow-1" style="padding-left: 0px!important;">
                        <a href="javascript:;"
                            class="add-to-cart-btn theme-btn radius-6 text-nowrap @if(!$product->getModel()->isAvailable()) disabled @endif"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->current_price }}"
                            data-checkout-url="{{ route('cart.checkout') }}"
                            data-quantity="1"
                            data-notspinner="true"
                            style="height:38px!important;
                            width:100%!important;
                            "
                        >{{ __('main.kupit') }}</a>
                    </div>
                </div>

                <div class="btn-items d-none d-xl-block">
                    <div class="btn-item flex-grow-1">
                        <a href="javascript:;"
                            class="add-to-cart-btn d-lg-none theme-btn radius-6 text-nowrap @if(!$product->getModel()->isAvailable()) disabled @endif"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->current_price }}"
                            data-quantity="1"
                        >{{ __('main.to_cart') }}</a>
                    </div>
                    <div class="btn-item d-lg-none">
                        <a href="javascript:;"
                            class="favorite-link icon-btn radius-6 @if(!app('wishlist')->get($product->id)) add-to-wishlist-btn @else remove-from-wishlist-btn active @endif only-icon"
                            data-id="{{ $product->id }}"
                            data-add-url="{{ route('wishlist.add') }}"
                            data-remove-url="{{ route('wishlist.delete', $product->id) }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->current_price }}"
                            data-add-text="<svg width='28' height='28' fill='#005bff'><use xlink:href='#heart'></use></svg>"
                            data-delete-text="<svg width='28' height='28' fill='#005bff'><use xlink:href='#heart'></use></svg>"
                        >
                        <svg width="28" height="28" fill="#005bff"><use xlink:href="#heart"></use></svg>
                        </a>
                    </div>
                </div>
                {{--
                <div class="btn-items">
                    <div class="btn-item flex-grow-1 d-lg-none">
                        <a href="javascript:;"
                            class="buy-in-one-click theme-btn radius-6 text-nowrap @if (!$product->getModel()->isAvailable()) disabled @endif"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->current_price }}"
                            data-fixPrice="{{ $product->fix_price }}"
                            data-quantity="1"
                            data-checkout-url="{{ route('cart.checkout') }}"
                            data-image="{{ $product->$showImg }}"
                            data-toggle="modal" data-target="#buy-in-one-click"
                            data-backdrop="static"
                        >{{ __('main.buy_in_one_click') }}</a>
                    </div>
                    <div class="btn-item d-lg-none hidden">
                        <a href="javascript:;"
                            class="favorite-link icon-btn radius-6 @if(!app('wishlist')->get($product->id)) add-to-wishlist-btn @else remove-from-wishlist-btn active @endif only-icon"
                            data-id="{{ $product->id }}"
                            data-add-url="{{ route('wishlist.add') }}"
                            data-remove-url="{{ route('wishlist.delete', $product->id) }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->current_price }}"
                            data-add-text="<svg width='28' height='28' fill='#005bff'><use xlink:href='#heart'></use></svg>"
                            data-delete-text="<svg width='28' height='28' fill='#005bff'><use xlink:href='#heart'></use></svg>"
                        >
                            <svg width="28" height="28" fill="#005bff"><use xlink:href="#heart"></use></svg>
                        </a>
                    </div>
                </div>
                --}}
            </div>
        </div>
    </div>
</div>


<!-- INTALLMENT PAYMENT MODAL -->
<div class="modal fade" id="buy-in-one-click" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{ __('main.buy_in_one_click') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <form action="{{ route('order.add') }}" method="POST" id="checkout-form" class="row checkout-form">
        @csrf
        <div class="modal-body" style="padding: 0px 12px!important;">
            <div class="row mt-2">
                <div class="col-5">
                    <img class="modal-image-show" src="" style="width: 143px!important; height: 140px!important;" class="img-fluid">
                </div>
                <div class="col-7">
                    <div class="row">
                        <div class="col-md-12">
                            <b><span class="modal-name-show"> </span></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 align-items-bottom mt-4" style="text-align: right!important;">
                            <span><span class="modal-price-show" style="
                                text-align: right!important;
                                color: #ff3d16!important;
                                font-weight: bolder!important;
                                "> </span> <b> {{ __("main.currency") }} </b></span>
                        </div>
                    </div>
                </div>
            </div>

            <hr>


            <div class="row px-2">
                <div class="col-md-8">
                    <div class="form-group" style="width: 100%!important;">
                        <label class="control-label">{{ __('main.form.your_name') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="name" id="storage_name" class="form-control  @error('name') is-invalid @enderror"
                            value="{{ old('name', optional(auth()->user())->name) }}" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group" style="width: 100%!important;">
                        <label class="control-label">
                            <span>{{ __('main.form.phone_number') }}</span>
                            <span class="text-danger">*</span>
                            {{-- <small class="text-muted">({{ __('main.phone_number_example') }})</small> --}}
                        </label>

                        <input type="tel" name="phone_number" id="storage_phone"
                            class="phone-input-mask form-control  @error('phone_number') is-invalid @enderror"
                            value="{{ old('phone_number', optional(auth()->user())->phone_number) ?? '' }}"
                            required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                        @error('phone_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    {{-- <inputtype="hidden"class="input-name-show"name="name"value=""> --}}
                    <input type="hidden" name="payment_method_id" value="1">
                    <input type="hidden" name="communication_method" value="2">
                </div>
            </div>

            <hr>

            <div class="row mb-3"> 
                <div class="col-6">
                    <a href="{{ route('cart.delete', $product->id) }}" class="close btn remove-from-cart-btn-prototype" data-dismiss="modal" aria-label="Close" style="font-size:16px; padding: 7px; display: block!important; width: 100%;">{{ __("main.cancel") }}</a>
                </div>
                <div class="col-6">
                    <button type="submit"
                    class="btn add-to-cart-btn-prototype place-order-btn"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->current_price }}"
                    data-quantity="1"
                    data-fixPrice="{{ $product->fix_price }}"
                    id="startConfetti"
                    style="
                    background-color: #F98329;
                    color:#ffffff;
                    font-size:16px;
                    padding: 7px;
                    display: block;
                    width: 100%;
                    ">{{ __("main.buy_immediately") }}</button>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
</div>
<!-- INSTALLMENT PAYMENT MODAL ends -->