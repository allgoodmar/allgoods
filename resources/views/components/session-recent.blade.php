@if (!$sessionRecentProducts->isEmpty())
    <section class="products">
        <div class="container">
            <div class="content-top">
                <h2>{{ __('main.recentlyseen') }}</h2>
                {{--
                <a href="{{ route('bestsellers') }}" class="more-link" data-mobile-text="{{ __('main.all') }}">
                    <span>{{ __('main.view_all') }}</span>
                    <svg width="18" height="18" fill="#6b7279">
                        <use xlink:href="#arrow"></use>
                    </svg>
                </a>
                --}}
            </div>
            @if (count($sessionRecentProducts) < 3)
                <div class="row products-wrap">
                    @foreach ($sessionRecentProducts as $product)
                        <div class="product-card__parent col-6">
                            @include('partials.product_one')
                        </div>
                    @endforeach
                </div>
            @else
                <div class="row products-wrap owl-carousel owl-theme">
                    @foreach ($sessionRecentProducts as $product)
                        <div class="product-card__parent col-12">
                            @include('partials.product_one')
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif
