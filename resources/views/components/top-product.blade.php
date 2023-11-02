@if (!$products->isEmpty())
    <section class="products mt-5">
        <div class="container">
            <div class="content-top">
                <h2>{{ __('main.we_also_recommend2') }}</h2>
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
