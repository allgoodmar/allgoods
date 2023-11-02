<div class="product-reviews__item">
    {{-- <h4>Фото покупателей</h4>
    <div class="reviews-images">
        <div class="reviews-images__item" data-src="img/products/301.png">
            <img src="img/products/301.png" alt="">
        </div>
        <div class="reviews-images__item" data-src="img/products/302.png">
            <img src="img/products/302.png" alt="">
        </div>
        <div class="reviews-images__item" data-src="img/products/301.png">
            <img src="img/products/301.png" alt="">
        </div>
        <div class="reviews-images__item" data-src="img/products/302.png">
            <img src="img/products/302.png" alt="">
        </div>
        <div class="reviews-images__item" data-src="img/products/301.png">
            <img src="img/products/301.png" alt="">
        </div>
        <div class="reviews-images__item" data-src="img/products/302.png">
            <img src="img/products/302.png" alt="">
        </div>
        <div class="reviews-images__item" data-src="img/products/301.png">
            <img src="img/products/301.png" alt="">
        </div>
        <div class="reviews-images__item" data-src="img/products/302.png">
            <img src="img/products/302.png" alt="">
        </div>
    </div> --}}
    <h5>{{ $review->name }}</h5>
    <div class="d-flex align-items-center mb-2">
        <div class="review-rating mr-2">
            @include('partials.stars_input', ['rating' => $review->rating])
        </div>
        {{-- <ul class="star-list">
            <li>
                <svg width="18" height="18" fill="#fea92e">
                    <use xlink:href="#star"></use>
                </svg>
            </li>
            <li>
                <svg width="20" height="20" fill="#fea92e">
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
        </ul> --}}
        <span>{{ Helper::formatDate($review->created_at, true) }}</span>
    </div>
    <p>{{ $review->body }}</p>
</div>
