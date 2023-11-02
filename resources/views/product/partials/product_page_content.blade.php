@php
    $addToCartAvailable = $product->getModel()->isAvailable() ? true : false;
@endphp
<div class="product-page-content" data-product-id="{{ $product->id }}">
    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>
    <section class="product-view">
        <div class="container">
            @can('edit', $product->getModel())
                <div class="my-4">
                    <a href="{{ url('admin/products/' . $product->id . '/edit') }}" class="btn btn-lg btn-primary"
                        target="_blank">Редактировать (SKU: {{ $product->sku }}, ID: {{ $product->id }})</a>
                    @if ($product->productGroup)
                    <a href="{{ url('admin/product_groups/' . $product->productGroup->id . '/settings') }}" class="btn btn-lg btn-info"
                        target="_blank">Группа товара ({{ $product->productGroup->name }}, ID: {{ $product->productGroup->id }})</a>
                    @endif
                </div>
            @endcan
            <h1>{{ $product->name }}</h1>
            <div class="product-view-nav">
                <div class="product-view-nav__item">
                    <ul class="star-list">
                        <li>
                            <svg width="18" height="18" fill="#fea92e">
                                <use xlink:href="#star"></use>
                            </svg>
                        </li>
                        <li>
                            <svg width="18" height="18" fill="#fea92e">
                                <use xlink:href="#star"></use>
                            </svg>
                        </li>
                        <li>
                            <svg width="18" height="18" fill="#fea92e">
                                <use xlink:href="#star"></use>
                            </svg>
                        </li>
                        <li>
                            <svg width="18" height="18" fill="#fea92e">
                                <use xlink:href="#star"></use>
                            </svg>
                        </li>
                        <li>
                            <svg width="18" height="18" fill="#fea92e">
                                <use xlink:href="#star"></use>
                            </svg>
                        </li>
                    </ul>
                    <a href="#page-reviews-block">{{ $reviewsQuantity }} {{ __('main.reviews2') }} </a>
                    {{-- 
                        <small class="ml-2"><a href="https://allgood.uz/uz/hotite-100-000-sum" class="btn btn-sm" style="background-color: #f98329; font-weight:bolder; color:white;"> {{ __('main.konkurs') }} </a></small> 
                    --}}
                    @php
                    // $ordersQuantity = $product->orderItems->count();
                        $ordersQuantity = $product->number_of_sales;
                    @endphp
                    @if ($ordersQuantity)
                        <i class="bi bi-bag-check-fill ml-3 d-lg-none" style="color: #F98329;"></i> <strong class="text-dark ml-1 d-lg-none"> {{ __('main.bought_times', ['quantity' => $ordersQuantity]) }}</strong>
                    @endif
                </div>
                @if ($ordersQuantity)
                    <div class="product-view-nav__item d-none d-xl-block">
                        <i class="bi bi-bag-check-fill mr-1" style="color: #F98329;"></i> <strong class="text-dark"> {{ __('main.bought_times', ['quantity' => $ordersQuantity]) }}</strong>
                    </div>
                @endif

                <span class="product-page-stock text-sm d-none">
                    {!! __('main.products_left2', ['quantity' => '<span class="product_page_in_stock">' . $product->getModel()->getStock() . '</span>']) !!}
                </span>

                <div class="product-view-nav__item d-none d-lg-flex">
                    <a href="javascript:;"
                        class="favorite-link @if(!app('wishlist')->get($product->id)) add-to-wishlist-btn @else remove-from-wishlist-btn active @endif"
                        data-id="{{ $product->id }}"
                        data-add-url="{{ route('wishlist.add') }}"
                        data-remove-url="{{ route('wishlist.delete', $product->id) }}"
                        data-name="{{ $product->name }}"
                        data-price="{{ $product->current_price }}"
                        data-add-text="<svg width='24' height='24' fill='#0b2031'><use xlink:href='#heart'></use></svg> <span>{{ __('main.to_featured') }}</span>"
                        data-delete-text="<svg width='24' height='24' fill='#0b2031'><use xlink:href='#heart'></use></svg> <span>{{ __('main.delete_from_featured') }}</span>"
                    >
                        @if(!app('wishlist')->get($product->id))
                            <svg width="24" height="24" fill="#0b2031">
                                <use xlink:href="#heart"></use>
                            </svg>
                            <span>{{ __('main.to_featured') }}</span>
                        @else
                            <svg width="24" height="24" fill="#0b2031">
                                <use xlink:href="#heart"></use>
                            </svg>
                            <span>{{ __('main.delete_from_featured') }}</span>
                        @endif

                    </a>
                </div>
                <div class="product-view-nav__item ml-auto d-none d-lg-flex">
                    <b>
                        <svg width="28" height="28">
                            <use xlink:href="#confirmation"></use>
                        </svg>
                        <span class="@if(!$product->getModel()->isAvailable()) @if($product->getModel()->getProductStatus() == 3) text-dark @else text-danger @endif @endif">
                            @if($product->getModel()->isAvailable() && $product->getModel()->getProductStatus() == \App\Product::STATUS_ACTIVE)
                                {{ __('main.in_stock') }}
                            @elseif ($product->getModel()->isAvailable() && $product->getModel()->getProductStatus() == \App\Product::STATUS_SOON)
                                {{ __('main.soon_in_stock') }}
                            @else 
                                {{ __('main.not_in_stock') }}
                            @endif
                        </span>
                    </b>
                </div>
                <div class="product-view-nav__item row">
                    <div class="product-view-nav__item d-lg-none">
                        <b>
                            <svg width="28" height="28">
                                <use xlink:href="#confirmation"></use>
                            </svg>
                            <span class="@if(!$product->getModel()->isAvailable()) @if($product->getModel()->getProductStatus() == 3) text-dark @else text-danger @endif @endif">
                                @if($product->getModel()->isAvailable() && $product->getModel()->getProductStatus() == \App\Product::STATUS_ACTIVE)
                                    {{ __('main.in_stock') }}
                                @elseif ($product->getModel()->isAvailable() && $product->getModel()->getProductStatus() == \App\Product::STATUS_SOON)
                                    {{ __('main.soon_in_stock') }}
                                @else 
                                    {{ __('main.not_in_stock') }}
                                @endif
                            </span>
                        </b>
                    </div>
                    <div class="product-view-nav__item ml-3">
                        <strong>{{ __('main.sku') }}: <span>{{ $product->sku }}</span></strong>
                    </div>
                </div>
            </div>

            <div class="row product-wrap">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="product-preview">
                                <div class="product-preview__thumbs swiper-container d-none d-lg-block">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="swiper-item radius-6">
                                                <img src="{{ $product->micro_img }}" alt="{{ $product->name }}" loading="lazy" class="img-fluid">
                                            </div>
                                        </div>
                                        @foreach ($product->micro_imgs as $key => $microImg)
                                            <div class="swiper-slide">
                                                <div class="swiper-item radius-6">
                                                    <img src="{{ $microImg }}" alt="{{ $product->name . ' ' . $key }}" loading="lazy" class="img-fluid">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="product-preview__swiper swiper-container">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="swiper-item radius-6 zoomImg">
                                                <strong class="bg-yellow installment-text radius-20 d-none d-lg-inline-block">{{ __('main.installment_payment') }}
                                                        0-0-12</strong>
                                                <a href="{{ $product->img }}" class="d-block" data-fancybox="gallery">
                                                    <img src="{{ $product->medium_img }}" alt="{{ $product->name }}" loading="lazy" class="img-fluid">
                                                </a>
                                            </div>
                                        </div>
                                        @foreach ($product->medium_imgs as $key => $meduimImg)
                                            <div class="swiper-slide">
                                                <div class="swiper-item radius-6 zoomImg">
                                                    <a href="{{ $product->imgs[$key] }}" class="d-block" data-fancybox="gallery">
                                                        <img src="{{ $meduimImg }}" alt="{{ $product->name . ' ' . $key }}" loading="lazy" class="img-fluid">
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-pagination d-lg-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">

                            @if ($productGroup)
                                @include('product.partials.product_group')
                            @endif

                            <div class="product-character d-none d-lg-block">
                                <ul class="product-character__list product-character__list-sm" data-target="more-container">
                                    @if ($brand)
                                        <li>
                                            <span>{{ __('main.brand') }}</span>
                                            <div class="col"></div>
                                            <strong>{{ $brand->name }}</strong>
                                        </li>
                                    @endif
                                    @if (!$attributes->isEmpty())

                                        @foreach ($attributes as $attribute)
                                            <li>
                                                <span>{{ $attribute->name }}</span>
                                                <div class="col"></div>
                                                <strong>
                                                    @foreach ($attribute->attributeValues as $attributeValue)
                                                        {{ $attributeValue->getTranslatedAttribute('name') }}@if (!$loop->last){{ ',' }}@endif
                                                    @endforeach
                                                </strong>
                                            </li>
                                            @if($loop->index > 5) @break @endif
                                        @endforeach

                                    @endif
                                </ul>
                                @if (!$attributes->isEmpty())
                                    <div class="my-3">
                                        <a href="javascript:;" class="show-all-specifications-btn">{{ __('main.all_specifications') }}</a>
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3">

                    @if (!empty($partnersPrices))
                    <div class="partner-installments-list my-4">
                        @foreach ($partnersPrices as $item)
                            @if (empty($item['prices']))
                                @continue
                            @endif
                            {{-- @if ($item['partner']->id == 2 && (!auth()->check() || auth()->user()->id != 335))
                                @continue
                            @endif --}}
                            @php
                                $firstItemPrice = $item['prices'][0];
                            @endphp
                            @if ($item['partner']->id == 1) {{-- default AllGood - 3 --}}
                                <div class="radius-14 mb-4 py-3 px-3 font-weight-bold partner-installment-block partner-installment-block-{{ $item['partner']->id }}">
                                    <div class="d-flex align-items-center justify-content-center flex-wrap">
                                        <span class="d-inline-block bg-yellow px-3 py-2 radius-20 text-nowrap partner-installment-price my-1 mx-2 ">{{ $firstItemPrice['price_per_month_formatted'] }}</span>
                                        <select name="partner_installment" class="form-control form-control-sm text-lowercase my-1 mx-2 select-partner-installment-{{ $item['partner']->id }} select-partner-installment" style="width: 118px;">
                                            @foreach ($item['prices'] as $itemPrice)
                                                <option value="{{ $itemPrice['partner_installment']->id }}" data-duration="{{ $itemPrice['partner_installment']->duration }}" data-price="{{ $itemPrice['price'] }}" data-price-formatted="{{ $itemPrice['price_formatted'] }}" data-price-per-month="{{ $itemPrice['price_per_month'] }}" data-price-per-month-formatted="{{ $itemPrice['price_per_month_formatted'] }}" data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $itemPrice['partner_installment']->id]) }}">
                                                    {{ $itemPrice['partner_installment']->duration }} {{ trans_choice('main.month3', $itemPrice['partner_installment']->duration % 20) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- <span class="partner-installment-block-radio-indicator"></span> --}}
                                    <div class="text-center mt-2">
                                        <a href="https://allgood.uz/dopuskaetsya-li-rassrochka-v-shariate" target="_blank" class="text-primary">@lang('main.rassrochka_v_islame')</a>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <button type="button" class="theme-btn radius-6 @if (!$product->getModel()->isAvailable()) disabled @endif" style="display: block; width: 100%;" @if ($product->getModel()->isAvailable()) data-toggle="modal" data-target="#exampleModalCenter" @endif >
                                            {{ __('main.installment_payment_buy') }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @endif


                    <!-- INTALLMENT PAYMENT MODAL -->

                    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLongTitle">{{ __('main.bnpl') }}</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body" style="padding: 0px 12px!important;">
                                @if (!empty($partnersPrices))
                                <div class="partner-installments-list my-4">
                                    @php
                                        $ii = 1;
                                    @endphp
                                    @foreach ($partnersPrices as $item)
                                        @if (empty($item['prices']))
                                            @continue
                                        @endif
                                        {{-- @if ($item['partner']->id == 2 && (!auth()->check() || auth()->user()->id != 335))
                                            @continue
                                        @endif --}}
                                        @php
                                            $firstItemPrice = $item['prices'][0];
                                        @endphp

                                        {{-- INTALLMENT STARTS --}}
                                        
                                        @if (!empty($brand) && $brand->name == 'Apple')
                                        @if($item['partner']->id != 3 && $item['partner']->id != 2)
                                            <div class="row radius-14 mb-1 py-2 px-1 font-weight-bold partner-installment-block partner-installment-block-{{ $item['partner']->id }}"
                                                style="border: none; box-shadow: none; padding-bottom: 0 !important; padding-top: 0 !important;">
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-4"></div>
                                                        <div class="col-4 mb-1 d-flex align-items-center justify-content-center">
                                                            <img src="{{ $item['partner']->img }}" alt="{{ $item['partner']->getTranslatedAttribute('name') }}"
                                                                class="img-fluid partner-installment-block-img">
                                                        </div>
                                                        <div class="col-4"></div>
                                                    </div>
    
                                                    <div class="row d-flex align-items-center justify-content-center flex-wrap">
                                                        <div class="col-6 d-flex align-items-center justify-content-end text-right">
                                                            <span
                                                                class="d-inline-block bg-yellow px-3 py-2 radius-20 text-nowrap partner-installment-price my-1 mx-2 ">{{ $firstItemPrice['price_per_month_formatted'] }}</span>
                                                        </div>
                                                        <div class="col-6 d-flex align-items-center justify-content-start text-left">
                                                            <select name="partner_installment"
                                                                class="form-control form-control-sm text-lowercase my-1 mx-2 select-partner-installment-{{ $item['partner']->id }} select-partner-installment"
                                                                style="width: 118px;">
                                                                @foreach ($item['prices'] as $itemPrice)
                                                                    <option value="{{ $itemPrice['partner_installment']->id }}"
                                                                        data-duration="{{ $itemPrice['partner_installment']->duration }}"
                                                                        data-price="{{ $itemPrice['price'] }}" data-price-formatted="{{ $itemPrice['price_formatted'] }}"
                                                                        data-price-per-month="{{ $itemPrice['price_per_month'] }}"
                                                                        data-price-per-month-formatted="{{ $itemPrice['price_per_month_formatted'] }}"
                                                                        data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $itemPrice['partner_installment']->id]) }}">
                                                                        {{ $itemPrice['partner_installment']->duration }}
                                                                        {{ trans_choice('main.month3', $itemPrice['partner_installment']->duration % 20) }}

                                                                        @if ($itemPrice['partner_installment']->duration == 3 && $item['partner']->id == 3)
                                                                            (выгодно)
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-sm-12">
                                                            <button type="button" class="theme-btn d-inline-flex radius-6 add-to-cart-btn"
                                                                style="background-color: #F98329; font-size:16px; padding: 7px; display: block; width: 100%;"
                                                                data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $firstItemPrice['partner_installment']->id]) }}"
                                                                data-id="{{ $product->id }}"
                                                                data-name="{{ $product->name }}"
                                                                data-price="{{ $product->current_price }}"
                                                                data-quantity="1">
                                                                {{ __('main.installment_payment_buy') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        @if ($category->id == 990)
                                            @if($item['partner']->id == 3)
                                                <div class="row radius-14 mb-1 py-2 px-1 font-weight-bold partner-installment-block partner-installment-block-{{ $item['partner']->id }}"
                                                    style="border: none; box-shadow: none; padding-bottom: 0 !important; padding-top: 0 !important;">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-4"></div>
                                                            <div class="col-4 mb-1 d-flex align-items-center justify-content-center">
                                                                <img src="{{ $item['partner']->img }}" alt="{{ $item['partner']->getTranslatedAttribute('name') }}"
                                                                    class="img-fluid partner-installment-block-img">
                                                            </div>
                                                            <div class="col-4"></div>
                                                        </div>
            
                                                        <div class="row d-flex align-items-center justify-content-center flex-wrap">
                                                            <div class="col-6 d-flex align-items-center justify-content-end text-right">
                                                                <span
                                                                    class="d-inline-block bg-yellow px-3 py-2 radius-20 text-nowrap partner-installment-price my-1 mx-2 ">{{ $firstItemPrice['price_per_month_formatted'] }}</span>
                                                            </div>
                                                            <div class="col-6 d-flex align-items-center justify-content-start text-left">
                                                                <select name="partner_installment"
                                                                    class="form-control form-control-sm text-lowercase my-1 mx-2 select-partner-installment-{{ $item['partner']->id }} select-partner-installment"
                                                                    style="width: 118px;">
                                                                    @foreach ($item['prices'] as $itemPrice)
                                                                        @if ($itemPrice['partner_installment']->duration != 12)
                                                                            <option value="{{ $itemPrice['partner_installment']->id }}"
                                                                                data-duration="{{ $itemPrice['partner_installment']->duration }}"
                                                                                data-price="{{ $itemPrice['price'] }}" data-price-formatted="{{ $itemPrice['price_formatted'] }}"
                                                                                data-price-per-month="{{ $itemPrice['price_per_month'] }}"
                                                                                data-price-per-month-formatted="{{ $itemPrice['price_per_month_formatted'] }}"
                                                                                data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $itemPrice['partner_installment']->id]) }}">
                                                                                {{ $itemPrice['partner_installment']->duration }}
                                                                                {{ trans_choice('main.month3', $itemPrice['partner_installment']->duration % 20) }}

                                                                                @if ($itemPrice['partner_installment']->duration == 3 && $item['partner']->id == 3)
                                                                                    (выгодно)
                                                                                @endif
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                                <br>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-sm-12">
                                                                <button type="button" class="theme-btn d-inline-flex radius-6 add-to-cart-btn @if (!$product->getModel()->isAvailable()) disabled @endif"
                                                                    style="background-color: #F98329; font-size:16px; padding: 7px; display: block; width: 100%;"
                                                                    data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $firstItemPrice['partner_installment']->id]) }}"
                                                                    data-id="{{ $product->id }}"
                                                                    data-name="{{ $product->name }}"
                                                                    data-price="{{ $product->current_price }}"
                                                                    data-quantity="1">
                                                                    {{ __('main.installment_payment_buy') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div class="row radius-14 mb-1 py-2 px-1 font-weight-bold partner-installment-block partner-installment-block-{{ $item['partner']->id }}"
                                                style="border: none; box-shadow: none; padding-bottom: 0 !important; padding-top: 0 !important;">
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-4"></div>
                                                        <div class="col-4 mb-1 d-flex align-items-center justify-content-center">
                                                            <img src="{{ $item['partner']->img }}" alt="{{ $item['partner']->getTranslatedAttribute('name') }}"
                                                                class="img-fluid partner-installment-block-img">
                                                        </div>
                                                        <div class="col-4"></div>
                                                    </div>
        
                                                    <div class="row d-flex align-items-center justify-content-center flex-wrap">
                                                        <div class="col-6 d-flex align-items-center justify-content-end text-right">
                                                            <span
                                                                class="d-inline-block bg-yellow px-3 py-2 radius-20 text-nowrap partner-installment-price my-1 mx-2 ">{{ $firstItemPrice['price_per_month_formatted'] }}</span>
                                                        </div>
                                                        <div class="col-6 d-flex align-items-center justify-content-start text-left">
                                                            <select name="partner_installment"
                                                                class="form-control form-control-sm text-lowercase my-1 mx-2 select-partner-installment-{{ $item['partner']->id }} select-partner-installment"
                                                                style="width: 118px;">
                                                                @foreach ($item['prices'] as $itemPrice)
                                                                    <option value="{{ $itemPrice['partner_installment']->id }}"
                                                                        data-duration="{{ $itemPrice['partner_installment']->duration }}"
                                                                        data-price="{{ $itemPrice['price'] }}" data-price-formatted="{{ $itemPrice['price_formatted'] }}"
                                                                        data-price-per-month="{{ $itemPrice['price_per_month'] }}"
                                                                        data-price-per-month-formatted="{{ $itemPrice['price_per_month_formatted'] }}"
                                                                        data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $itemPrice['partner_installment']->id]) }}">
                                                                        {{ $itemPrice['partner_installment']->duration }}
                                                                        {{ trans_choice('main.month3', $itemPrice['partner_installment']->duration % 20) }}

                                                                        @if ($itemPrice['partner_installment']->duration == 3 && $item['partner']->id == 3)
                                                                            (выгодно)
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-sm-12">
                                                            <button type="button" class="theme-btn d-inline-flex radius-6 add-to-cart-btn @if (!$product->getModel()->isAvailable()) disabled @endif"
                                                                style="background-color: #F98329; font-size:16px; padding: 7px; display: block; width: 100%;"
                                                                data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $firstItemPrice['partner_installment']->id]) }}"
                                                                data-id="{{ $product->id }}"
                                                                data-name="{{ $product->name }}"
                                                                data-price="{{ $product->current_price }}"
                                                                data-quantity="1">
                                                                {{ __('main.installment_payment_buy') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                         @endif
                                         {{-- INTALLMENT ENDS --}}


                                        @if ($ii == 1 || $ii == 2)
                                            <hr>
                                        @endif

                                        @php
                                            $ii++;
                                        @endphp
                                    @endforeach
                                </div>
                                @endif
                            </div>
                          </div>
                        </div>
                      </div>

                    <!-- INSTALLMENT PAYMENT MODAL ends -->


                    <div class="product-about radius-14 px-3 py-2 d-none d-xl-block" style="box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.15)">
                        @if ($product->getModel()->isDiscounted())
                            <strong class="bg-danger radius-20 sale-text remove-del">-{{ $product->getModel()->discount_percent }}%</strong>
                        @endif
                        @if ($product->getModel()->isDiscounted())
                            <p class="text-price center-element">{{ Helper::formatPrice($product->current_price) }}</p>
                        @else
                            <p class="text-price text-center center-element">{{ Helper::formatPrice($product->current_price) }}</p>
                        @endif
                        @if ($product->getModel()->isDiscounted())
                            <del class="remove-del">{{ Helper::formatPrice($product->price) }}</del>
                            @if ($product->sale_end_date > now())
                                <br class="remove-del"><span class="badge bg-success countdown-sale text-white p-2"><i class="bi bi-clock"></i> </span>
                            @endif
                        @endif

                        <!-- cart -->
                        <button type="button"
                                class="theme-btn radius-6 product-page-add-to-cart-btn add-to-cart-btn
                                @if (!$product->getModel()->isAvailable()) disabled @endif"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->current_price }}"
                                data-quantity="1"
                                style="display: block; width: 100%;"
                        >
                            </i> {{ __('main.add_to_cart') }}
                        </button>

                        <button type="button"
                                class="theme-btn radius-6 product-page-add-to-cart-btn add-to-cart-btn
                                @if (!$product->getModel()->isAvailable()) disabled @endif"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->current_price }}"
                                data-quantity="1"
                                data-checkout-url="{{ route('cart.checkout') }}"
                                style="display: block; width: 100%;"
                        >
                            </i> {{ __('main.buy_in_one_click') }}
                        </button>
                        <!-- end cart -->

                        <p class="d-lg-none">{{ $product->description }}</p>
                    </div>

                    @if (isset($product->seller_id))
                        <div class="product-about radius-14 px-3 py-2" style="box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.15)">
                            <img src="{{ asset('img/seller_icon_big.png') }}" width="44" height="44" class="rounded-circle"> <b> Продавец: </b> <u> <a href="{{ route('seller.visit', ['id'=>$product->seller_id]) }}">{{ $seller->company_name }}</a> </u>
                        </div>
                    @endif

                    <div class="product-faq d-none">
                        <h5>{{ __('main.frequently_asked_questions') }}</h5>
                        <ul class="product-faq__links">
                            @foreach ($faqPages as $faqPage)
                                <li>
                                    <a href="{{ $faqPage->url }}">{{ $faqPage->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <ul class="product-faq__list">
                            <li>
                                <svg width="28" height="28" fill="#005bff">
                                    <use xlink:href="#credit-card"></use>
                                </svg>
                                <b>{{ __('main.secure_payment') }}</b>
                            </li>
                            <li>
                                <svg width="28" height="28" fill="#005bff">
                                    <use xlink:href="#undo"></use>
                                </svg>
                                <b>{{ __('main.easy_return_guarantee') }}</b>
                            </li>
                            <li>
                                <svg width="28" height="28" fill="#005bff">
                                    <use xlink:href="#delivery"></use>
                                </svg>
                                <b>{{ __('main.delivery_in_tashkent_in_24_hours') }}</b>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (!empty($youtubelinks) && !empty($youtubelinksOne))
        <div class="container">
            <h4>Видеообзоры</h4>
            <div class="container_playlist mb-4">
                <div class="main_video">
                    <iframe src="{{ $youtubelinksOne->link }}" title="The New Chevy Silverado EV Is the Electric Future of the Pickup Truck" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>

                <!-- PLAYLIST -->
                <div class="playlist">
                    @foreach ($youtubelinks as $item)
                        <div class="details_info active_video_play">
                            <source src="{{ $item->link }}">
                            <img src="{{ $item->thumbnail }}" loading="lazy" class="card-img" alt="">
                            <h4>{{ $item->name }}</h4>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <section class="product-descr" id="product-descr-container">
        <div class="container">
            <div class="product-descr__wrap">
                <ul class="product-descr__nav nav d-none d-lg-flex">
                    <li>
                        <a href="#product-characteristics" class="active" data-toggle="tab">{{ __('main.specifications') }}</a>
                    </li>
                    <li>
                        <a href="#product-description" data-toggle="tab">{{ __('main.description') }}</a>
                    </li>
                </ul>
                <div class="product-descr__content tab-content d-none d-lg-block">
                    <div class="tab-pane active" id="product-characteristics">
                        <ul class="product-character__list">
                            @if ($brand)
                                <li>
                                    <span>{{ __('main.brand') }}</span>
                                    <div class="col"></div>
                                    <strong><a href="{{ $brand->url }}">{{ $brand->name }}</a></strong>
                                </li>
                            @endif
                            @if (!$attributes->isEmpty())
                                @foreach ($attributes as $attribute)
                                    <li>
                                        <span>{{ $attribute->name }}</span>
                                        <div class="col"></div>
                                        <strong>
                                            @foreach ($attribute->attributeValues as $attributeValue)
                                                {{ $attributeValue->getTranslatedAttribute('name') }}@if (!$loop->last){{ ',' }}@endif
                                            @endforeach
                                        </strong>
                                    </li>
                                @endforeach
                            @endif
                            @if (!empty($model_name))
                                <li>
                                    <span>{{ __('main.model') }}</span>
                                    <div class="col"></div>
                                    <strong><a href="{{ $model_name->url }}">{{ $model_name->name }}</a></strong>
                                </li>
                            @endif
                        </ul>

                        @if (trim($product->specifications))
                            <div data-target="more-container">
                                {!! $product->specifications !!}
                            </div>
                            <a href="javascript:;" data-toggle="more-btn">{{ __('main.show_full') }}</a>
                        @endif

                    </div>
                    <div class="tab-pane" id="product-description">
                        <div data-target="more-container">
                            {!! $product->body !!}
                        </div>
                        <a href="javascript:;" data-toggle="more-btn">{{ __('main.show_full') }}</a>
                    </div>
                </div>

                <!-- mobile description -->
                <div class="product-descr-m d-lg-none">

                    <h3>{{ __('main.description') }}</h3>
                    <div class="text-block mb-4">
                        {!! $product->body !!}
                    </div>

                    @if (!$attributes->isEmpty())
                        <h3>{{ __('main.specifications') }}</h3>
                        <ul class="product-descr-m__list" data-target="more-container">
                            {{-- <li><h4>Экран</h4></li> --}}
                            @if ($brand)
                                <li>
                                    <span>{{ __('main.brand') }}</span>
                                    <div class="col"></div>
                                    <strong><a href="{{ $brand->url }}">{{ $brand->name }}</a></strong>
                                </li>
                            @endif
                            @foreach ($attributes as $attribute)
                                <li>
                                    <span>{{ $attribute->name }}</span>
                                    <div class="col"></div>
                                    <strong>
                                        @foreach ($attribute->attributeValues as $attributeValue)
                                            {{ $attributeValue->name }}@if (!$loop->last){{ ',' }}@endif
                                        @endforeach
                                    </strong>
                                </li>
                            @endforeach
                            @if ($parent_model_name)
                                <li>
                                    <span>{{ __('main.model') }}</span>
                                    <div class="col"></div>
                                    <strong><a href="{{ $model_name->url }}">{{ $model_name->name }}</a></strong>
                                </li>
                            @endif
                        </ul>
                    @endif
                    @if ($attributes->count() > 6)
                        <a href="javascript:;" data-toggle="more-btn">
                            <svg width="14" height="14" fill="#4482ff">
                                <use xlink:href="#arrow"></use>
                            </svg>
                            <span>{{ __('main.all_specifications') }}</span>
                        </a>
                    @endif
                </div>
                <!-- end mobile description -->

                @include('partials.reviews', ['reviewable_id' => $product->id, 'reviewable_type' => 'product'])

            </div>
        </div>
    </section>

    {{-- 

    <x-related-products :product-id="$product->id"></x-related-products>

    <x-similar-products :product-id="$product->id"></x-similar-products>

    <x-session-recent></x-session-recent>

    --}}

    <div class="product-page-nav-bottom nav-bottom d-lg-none"  style="box-shadow: 0 0 5px 5px #e5e5e5!important;">
        <div class="nav-bottom-buttons row">
            <div class="nav-bottom-buttons__item col text-center">
                @if ($product->getModel()->isDiscounted())
                    <span class="font-weight-bold" style="color: #ff3d16!important;">{{ Helper::formatPriceWithoutCurrency($product->current_price) }}</span> <br>
                @else
                    <span class="font-weight-bold" style="color: #ff3d16!important;">{{ Helper::formatPriceWithoutCurrency($product->current_price) }}</span> <br>
                @endif
                @if ($product->getModel()->isDiscounted())
                    <del style="color: gray;">{{ Helper::formatPriceWithoutCurrency($product->price) }}</del>
                @endif
            </div>
            <div class="nav-bottom-buttons__item col flex-grow-1">
                <button type="button"
                        class="theme-btn radius-6 product-page-add-to-cart-btn add-to-cart-btn @if (!$product->getModel()->isAvailable()) disabled @endif"
                        style="background-color: #F98329; font-size:16px; padding: 7px; display: block; width: 100%;"
                        data-id="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        data-price="{{ $product->current_price }}"
                        data-checkout-url="{{ route('cart.checkout') }}"
                        data-quantity="1"
                >
                    </i> {{ __('main.kupit') }}
                </button>
            </div>
        </div>
    </div>
</div>
