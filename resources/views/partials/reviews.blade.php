<div class="product-reviews" id="page-reviews-block">
    <h3>{{ __('main.reviews') }}</h3>

    @if(!$reviews->isEmpty())
        <div class="reviews">
            @foreach($reviews as $review)
                @include('partials.review_one')
            @endforeach
        </div>
    @else
        <div class="pb-4">
            {{ __('main.no_reviews') }}
        </div>
    @endif

    <div>
        <button class="theme-btn radius-6" data-toggle="collapse" data-target="#review-create">
            {{ __('main.write_a_review') }}
        </button>
        <div id="review-create" class="collapse">
            <br>
            <form action="{{ route('reviews.store') }}" method="post" class="review-form">

                @csrf

                <div class="form-hide-blocks">

                    <input type="hidden" name="reviewable_id" value="{{ $reviewable_id }}">
                    <input type="hidden" name="reviewable_type" value="{{ $reviewable_type }}">

                    <div class="form-group">
                        <label for="review_name" >{{ __('main.form.your_name') }} *</label>
                        <input type="text" name="name" id="review_name" class="form-control" value="@auth{{ auth()->user()->name }}@endauth" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">{{ __('main.phone_number') }} *</label>
                        <input type="tel" name="phone_number"
                                class="phone-input-mask2 form-control  @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number', optional(auth()->user())->phone_number) ?? '' }}"
                                required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">

                        <input type="hidden" name="email" id="review_email" class="form-control" value="test@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('main.mark') }}</label>
                        @include('partials.rating_field')
                    </div>
                    <div class="form-group">
                        <label for="review_body">{{ __('main.form.message') }} *</label>
                        <textarea name="body" id="review_body" class="form-control" required></textarea>
                    </div>

                    {{-- <div class="row gutters-5 mb-4">
                        <div class="col-xl-3 col-lg-6 mb-3 mb-lg-0">
                            <input type="text" name="captcha" class="form-control" placeholder="{{ __('main.form.security_code') }}" required>
                        </div>
                        <div class="col-xl-6 col-lg-6">
                            <div class="captcha-container">
                                <img src="{{ asset('images/captcha.png') }}" alt="Captcha" class="img-fluid rounded">
                            </div>
                        </div>
                    </div> --}}

                    <div class="form-group">
                        <button type="submit" class="theme-btn radius-6">{{ __('main.form.send') }}</button>
                    </div>

                </div>

                <div class="form-result"></div>

            </form>
        </div>
    </div>
</div>
