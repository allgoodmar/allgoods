@extends('layouts.print')

@section('seo_title', $product->name)

@section('content')

    <div class="container pt-4 pb-5">
        <h1 class="main-header mt-3 mb-2">{{ $product->name }}</h1>

        @php
            $rating = $product->rating_avg;
            $ratingCount = $product->active_reviews_count;
            if ($rating == 0) {
                $rating = 5;
            }
        @endphp
        <div class="product-page-rating mb-4">
            @include('partials.stars', ['rating' => $rating, 'rating_count' => $ratingCount])
        </div>

        @if ($brand)
            <div>
                <a href="{{ $brand->url }}"
                    class="font-weight-semibold">{{ $brand->name }}</a>
            </div>
        @endif
        @if ($product->sku)
            <div>
                <span class="text-gray">{{ __('main.sku') }}: {{ $product->sku }}</span>
            </div>
        @endif

        <div>
            <img src="{{ $product->large_img }}" class="img-fluid" alt="{{ $product->name }}" >
        </div>

        <div class="mb-4">
            @foreach ($product->micro_imgs as $key => $value)
                <img src="{{ $value }}" class="img-fluid m-3" alt="{{ $product->name }} {{ $key }}">
            @endforeach
        </div>

        <div class="product-page-price mb-4">
            <div>
                <span class="text-nowrap">
                    <span class="product_page_current_price current-price @if ($product->getModel()->isDiscounted()) special-price @endif">
                        {{ Helper::formatPrice($product->current_price) }}
                    </span>
                </span>
                @if ($product->getModel()->isDiscounted())
                    <span class="product_page_old_price old-price text-nowrap">
                        {{ Helper::formatPrice($product->price) }}
                    </span>
                @endif
            </div>
            @if (!$product->installmentPlans->isEmpty())
                <div class="min-price-per-month">
                    <span class="product_page_min_price_per_month"></span>
                    <span class="text-primary product_page_min_price_per_month_info_text">{{ __('main.installment_payment2') }}</span>
                </div>
            @endif

        </div>

        <div>
            @if ($product->body)
                <div class="text-block mb-4">
                    {!! $product->body !!}
                </div>
            @endif

            @if ($brand || !$attributes->isEmpty())
                <div class="box mb-4">
                    <h5 class="small-header">{{ __('main.specifications') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless specifications-table">
                            @if ($brand)
                                <tr>
                                    <td>
                                        <span>{{ __('main.brand') }}</span>
                                    </td>
                                    <td>{{ $brand->name }}</td>
                                </tr>
                            @endif
                            @if (!$attributes->isEmpty())
                                @foreach ($attributes as $attribute)
                                    <tr>
                                        <td>
                                            <span>{{ $attribute->name }}</span>
                                        </td>
                                        <td>
                                            @foreach ($attribute->attributeValues as $attributeValue)
                                                {{ $attributeValue->name }}@if (!$loop->last){{ ',' }}@endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                        </table>
                    </div>
                </div>
            @endif

            @if ($deliveryText)
                <div class="text-block">
                    {!! $deliveryText !!}
                </div>
            @endif
        </div>

    </div>

    <script>
        window.print();
    </script>

@endsection
