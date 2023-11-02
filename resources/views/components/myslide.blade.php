<section class="hero-banner d-none d-xl-block" style="padding-bottom: 10px; width: 100%!important;">
    <div class="container">
        <div class="hero-banner-swiper swiper-container">
            <div class="swiper-wrapper">
                @foreach ($slides as $slide)
                    <div class="swiper-slide" style="width: 100%!important; height:100%;">
                        <a href="{{ $slide->url }}" style="width: 100%!important;">
                            <div class="hero-banner-swiper__item banner_height_mb p-1">
                                <img src="{{ $slide->img }}" alt="{{ $slide->name }}" alt="image_banner" class="img-fluid">
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            {{-- <div class="swiper-pagination"></div> --}}
        </div>
    </div>
</section>

<section class="hero-banner d-xl-none" style="padding-bottom: 10px; width: 100%!important;">
    <div class="container">
        <div class="hero-banner-swiper swiper-container">
            <div class="swiper-wrapper">
                @foreach ($slides_mini as $slide)
                    <div class="swiper-slide" style="width: 100%!important; height:100%;">
                        <a href="{{ $slide->url }}" style="width: 100%!important;">
                            <div class="hero-banner-swiper__item banner_height_mb p-1">
                                <img src="{{ $slide->img }}" alt="{{ $slide->name }}" alt="image_banner" class="img-fluid">
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            {{-- <div class="swiper-pagination"></div> --}}
        </div>
    </div>
</section>

<section class="hero-banner" style="padding-top: 10px; padding-bottom: 10px;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="hero-banner-swiper swiper-container">
                    <div class="swiper-wrapper">
                        @foreach ($slides_medium_left as $slide)
                            <div class="swiper-slide">
                                <a href="{{ $slide->url }}">
                                    <div class="hero-banner-swiper__item banner_height_mb p-1" style="background-color: transparent!important;">
                                        <img src="{{ $slide->img }}" alt="{{ $slide->name }}" class="img-fluid">
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    {{-- <div class="swiper-pagination"></div> --}}
                </div>
            </div>
            <div class="col-md-6">
                <div class="hero-banner-swiper swiper-container">
                    <div class="swiper-wrapper">
                        @foreach ($slides_medium_right as $slide)
                            <div class="swiper-slide">
                                <a href="{{ $slide->url }}">
                                    <div class="hero-banner-swiper__item banner_height_mb p-1">
                                        <img src="{{ $slide->img }}" alt="{{ $slide->name }}" class="img-fluid">
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    {{-- <div class="swiper-pagination"></div> --}}
                </div>
            </div>
        </div>
    </div>
</section>

{{-- EIGHT BANNERS 
<section class="hero-banner d-none d-xl-block" style="padding-top: 10px; padding-bottom: 10px;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="hero-banner-swiper swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($slides_small_1 as $slide)
                                    <div class="swiper-slide" style="width: 100%!important; height:155px;">
                                        <a href="{{ $slide->url }}">
                                            <div class="hero-banner-swiper__item banner_height_mb p-1">
                                                <img src="{{ $slide->img }}" alt="{{ $slide->name }}" class="img-fluid">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="hero-banner-swiper swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($slides_small_2 as $slide)
                                    <div class="swiper-slide" style="width: 100%!important; height:155px;">
                                        <a href="{{ $slide->url }}">
                                            <div class="hero-banner-swiper__item banner_height_mb p-1">
                                                <img src="{{ $slide->img }}" alt="{{ $slide->name }}" class="img-fluid">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="hero-banner-swiper swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($slides_small_3 as $slide)
                                    <div class="swiper-slide" style="width: 100%!important; height:155px;">
                                        <a href="{{ $slide->url }}">
                                            <div class="hero-banner-swiper__item banner_height_mb p-1">
                                                <img src="{{ $slide->img }}" alt="{{ $slide->name }}" class="img-fluid">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="hero-banner-swiper swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($slides_small_4 as $slide)
                                    <div class="swiper-slide" style="width: 100%!important; height:155px;">
                                        <a href="{{ $slide->url }}">
                                            <div class="hero-banner-swiper__item banner_height_mb p-1">
                                                <img src="{{ $slide->img }}" alt="{{ $slide->name }}" class="img-fluid">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>--}}

@section('styles')
    <style>
        .hero-banner-swiper__item{
            background-color: transparent!important;
        }
        .hero-banner-swiper__item img{
            border-radius: 14px!important;
        }
    </style>
@endsection
