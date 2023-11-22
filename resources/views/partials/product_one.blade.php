@php
    if (!isset($size)) {
        $size = 'small';
    }
    $showImg = $size . '_img';
    $showSecondImg = 'second_' . $size . '_img';
@endphp

<div class="product-card product-one product-one-{{ $size }} {{ $productClass ?? '' }} bestseller_card_header shadow py-2 px-2" style="border-radius: 12px;">

    {{--
    @if ($product->getModel()->isBestseller())
        <span class="bestseller_card"><i class="bi bi-star-fill"></i> Топ продаж</span>
    @endif
    --}}

    <div class="product-card__header">
        <a href="javascript:;"
            class="favorite-link add-to-wishlist-btn-analytics @if(!app('wishlist')->get($product->id)) add-to-wishlist-btn @else remove-from-wishlist-btn active @endif only-icon"
            data-id="{{ $product->id }}"
            data-add-url="{{ route('wishlist.add') }}"
            data-remove-url="{{ route('wishlist.delete', $product->id) }}"
            data-name="{{ $product->name }}"
            data-price="{{ $product->current_price }}"
            data-add-text="<svg width='24' height='24' fill='#0b2031'><use xlink:href='#heart'></use></svg>"
            data-delete-text="<svg width='24' height='24' fill='#0b2031'><use xlink:href='#heart'></use></svg>"
            >
            <svg width="24" height="24" fill="#0b2031"><use xlink:href="#heart"></use></svg>
        </a>
        <a href="{{ $product->url }}" class="d-block">
            <img src="{{ $product->$showImg }}" loading="lazy" alt="{{ $product->name }}" class="img-fluid">
            <div class="status-list">
                {{--
                @if ($product->getModel()->isBestseller())
                    <span class="status green">Top</span>
                @endif
                --}}

                @if ($product->getModel()->isPromotion())
                    <span class="status purple">{{ __('main.promotion') }}</span>
                @endif
                @if ($product->getModel()->isDiscounted())
                    <span class="status red">-{{ $product->getModel()->discount_percent }}%</span>
                    {{-- <span class="badge bg-success countdown-sale text-white"><i class="bi bi-clock"></i> {{ __('main.hurry_up') }}</span> --}}
                @endif
                @if (!$product->getModel()->isAvailable())
                    <span class="status gray rounded">{{ __('main.not_in_stock') }}</span>
                @endif
                @if ($product->getModel()->isRadiocom())
                    <span class="status isRadiocom rounded border border-danger" style="background-color: white; color: black;"><i style="color: red;"><i style="color: black;">by</i> RADIOCOM </i> </span>
                @endif
            </div>
        </a>
        {{-- <div class="product-card-stickers d-none d-lg-block">
            @if (!$product->getModel()->isAvailable())
            <span class="d-inline-block px-2 py-1 small bg-light text-dark rounded">{{ __('main.not_in_stock') }}</span>
            @endif
        </div> --}}
    </div>
    <div class="product-card__content">
        <div class="product-card__body text-center" style="padding-top: 16px!important; padding-bottom: 0px!important;">
            <a href="{{ $product->url }}" class="d-block">

                {{-- @if($product->getModel()->isDiscounted())
                    <del class="old-price text-nowrap">
                        {{ Helper::formatPrice($product->price) }}
                    </del>
                @else
                    <span class="old-price">
                        &nbsp;
                    </span>
                @endif --}}
                <span class="title-link" style="margin-bottom: 5px!important;">{{ $product->name }}</span>

                {{--
                <div class="product-one-rating star-center">
                    @php
                        $rating = $product->rating_avg;
                        if ($rating == 0) {
                            $rating = 5;
                        }
                    @endphp
                    @include('partials.stars', ['rating' => $rating])
                </div>
                 --}}

                <p class="text-price text-nowrap" style="margin-top: 5px;">{{ Helper::formatPrice($product->current_price) }}</p>

                <div class="flex">
                    {{-- OLD PRICE
                    <strong class="bg-orange text-white text-nowrap radius-4">{{ __('main.price_per_month', ['price' => Helper::formatPrice($product->min_price_per_month)]) }}</strong>
                    --}}
                    <strong>{{ __('main.price_per_month', ['price' => Helper::formatPrice($product->min_price_per_month)]) }}</strong>
                </div>



            </a>
        </div>
        <div class="product-card__footer">
            <div class="btn-items" style="height: 42px!important;">
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
                <div class="btn-item d-flex col-8" style="padding-left: 0px!important">
                    <a href="javascript:;"
                        class="add-to-cart-btn theme-btn radius-6 text-nowrap @if(!$product->getModel()->isAvailable()) disabled @endif widtOfButton"
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

        </div>
    </div>
</div>
