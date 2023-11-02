@if (!$bestsellerProducts->isEmpty())
    <section class="products">
        <div class="container">
            <div class="content-top">
                <h2><a href="{{ route('bestsellers') }}" class="text-dark">{{ __('main.viborPolzovateley') }}</a></h2>
                <a href="{{ route('bestsellers') }}" class="more-link" data-mobile-text="{{ __('main.all') }}">
                    <span>{{ __('main.view_all') }}</span>
                    <svg width="18" height="18" fill="#6b7279">
                        <use xlink:href="#arrow"></use>
                    </svg>
                </a>
            </div>
            <div class="row products-wrap owl-carousel owl-theme">
                @foreach ($bestsellerProducts as $product)
                    <div class="product-card__parent col-12">
                        @include('partials.product_one')
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
