@if ($product)
    <div class="mb-4" data-aos="fade" data-aos-once="true" data-aos-delay="100">
        @include('partials.product_one', ['productClass' => 'day-product-one', 'stickerDay' => 1])
    </div>
@endif
