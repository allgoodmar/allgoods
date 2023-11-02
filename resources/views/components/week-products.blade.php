@if (!$products->isEmpty())
    <section class="products">
        <div class="container">
            <div class="content-top">
                <h2><a href="https://allgood.uz/category/916-akciya-tovary-nedeli" class="text-dark">{{ __('main.weeklyProducts') }}</a></h2>
                <a href="https://allgood.uz/category/916-akciya-tovary-nedeli" class="more-link" data-mobile-text="{{ __('main.all') }}">
                    <span>{{ __('main.view_all') }}</span>
                    <svg width="18" height="18" fill="#6b7279">
                        <use xlink:href="#arrow"></use>
                    </svg>
                </a>
            </div>
            <div class="row products-wrap owl-carousel owl-theme">
                @foreach ($products as $product)
                    <div class="product-card__parent py-2 col-12">
                        @include('partials.product_one')
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
