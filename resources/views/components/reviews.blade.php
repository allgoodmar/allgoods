<section class="hero-banner" style="padding-top: 10px; padding-bottom: 10px;">
    <div class="container">
        <div class="content-top m-0 p-0 mb-3">
            <h2 class="m-0 p-0">{{ __("main.otziviPolzovateley") }}</h2>
        </div>
        <div class="row feedback_txt">
            <div class="col-6 w-100">
                <div class="hero-banner-swiper swiper-container">
                    <div class="swiper-wrapper">
                        @foreach ($reviews as $item)
                            <a href="/product/{{ $item->product_id }}-{{ $item->product_slug }}" class="w-100 swiper-slide shadow" target="_blank">
                                <div class="feeds mt-2">
                                    <div class="img__sec">
                                        <img src="{{ asset('storage/'.$item->product_image) }}" alt="Text">
                                    </div>
                                    <ul class="info_txt">

                                        <li class="text_user_feed mt-1" style="font-size: 14px!important;">
                                            {{ strip_tags($item->body) }}
                                        </li>
                                        <li class="user__name">Отзыв дня от
                                            {{ Str::ucfirst($item->name) }}
                                        </li>
                                        {{-- <div class="shaddow_text_effects"></div> --}}
                                    </ul>
                                    <img src="{{ asset('/img/gifpointer.gif') }}" class="pointer" alt="">
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-6 w-100 d-none d-lg-block">
                <div class="hero-banner-swiper swiper-container">
                    <div class="swiper-wrapper">
                        @foreach ($reviews2 as $item)
                        <a href="/product/{{ $item->product_id }}-{{ $item->product_slug }}" class="w-100 swiper-slide shadow" target="_blank">
                            <div class="feeds mt-2">
                                <div class="img__sec">
                                    <img src="{{ asset('storage/'.$item->product_image) }}" alt="Text">
                                </div>
                                <ul class="info_txt">

                                    <li class="text_user_feed mt-1" style="font-size: 14px!important;">
                                        {{ strip_tags($item->body) }}
                                    </li>
                                    <li class="user__name">Отзыв дня от
                                        {{ Str::ucfirst($item->name) }}
                                    </li>
                                    {{-- <div class="shaddow_text_effects"></div> --}}
                                </ul>
                                <img src="{{ asset('/img/gifpointer.gif') }}" class="pointer" alt="">
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

