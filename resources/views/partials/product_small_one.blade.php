@php
    if (!isset($size)) {
        $size = 'micro';
    }
    $showImg = $size . '_img';
@endphp
<div class="product-small-one product-small-one-{{ $size }} {{ $productClass ?? '' }}">
    <div class="product-one-img">
        <a href="{{ $product->url }}">
            <img src="{{ $product->$showImg }}" alt="{{ $product->name }}" class="img-fluid">
        </a>
    </div>
    <div class="product-one-content">
        @php
            $rating = $product->rating_avg;
            if ($rating == 0) {
                $rating = 5;
            }
        @endphp
        <h4 class="product-one-title"><a href="{{ $product->url }}">{{ $product->name }}</a></h4>
        <div class="product-one-rating">
            @include('partials.stars', ['rating' => $rating])
        </div>

        <div class="product-one-price">
            <div class="min-price-per-month">
                {{ __('main.price_per_month', ['price' => Helper::formatPrice($product->min_price_per_month)]) }}
            </div>
            {{-- <div class="current-price">
                {{ Helper::formatPrice($product->installment_price) }}
            </div> --}}
            @if($product->getModel()->isDiscounted())
                <div class="old-price">
                    {{ Helper::formatPrice($product->price) }}
                </div>
            @endif
            <div class="current-price @if($product->getModel()->isDiscounted()) special-price @endif">
                {{ Helper::formatPrice($product->current_price) }}
            </div>
        </div>
    </div>
</div>
