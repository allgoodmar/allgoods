@extends('layouts.app')

@section('seo_title', $page->seo_title ? $page->seo_title : $page->name)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@php
    $phone = setting('contact.phone');
    $phone2 = setting('contact.phone2');
    $email = setting('contact.email');
@endphp

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <h1>{{ $page->name }}</h1>

        <div class="row mb-5">
            <div class="col-lg-6 order-lg-2 mb-5 mb-lg-0">

                <h3 class="contact-title mb-4">{{ __('main.our_contacts') }}</h3>

                <div class="media contact-info mb-3">
                    <i class="fa fa-map-marker mr-3 mt-1"></i>
                    <div class="media-body">
                        <span>{{ $address }}</span>
                    </div>
                </div>
                <div class="media contact-info mb-3">
                    <i class="fa fa-phone mr-3 mt-1"></i>
                    <div class="media-body">
                        <span><a href="tel:{{ Helper::phone($phone) }}" class="black-link">{{ $phone }}</a></span>
                        @if ($phone2)
                            <br>
                            <span><a href="tel:{{ Helper::phone($phone2) }}" class="black-link">{{ $phone2 }}</a></span>
                        @endif
                    </div>
                </div>
                <div class="media contact-info mb-3">
                    <i class="fa fa-envelope mr-3 mt-1"></i>
                    <div class="media-body">
                        <span><a href="mailto:{{ $email }}" class="black-link">{{ $email }}</a></span>
                    </div>
                </div>

                <div class="contact-map my-4">
                    {!! setting('contact.map') !!}
                </div>

            </div>
            <div class="col-lg-6 order-lg-1">

                <h3 class="contact-title">{{ __('main.write_us') }}</h3>

                <form class="contact-form" method="post"  action="{{ route('contacts.send') }}">

                    @csrf

                    <div class="form-result"></div>

                    <div class="form-group">
                        <label for="form_name">{{ __('main.form.your_name') }}&nbsp;<span class="text-danger">*</span></label>
                        <input class="form-control" name="name" id="form_name" type="text" required>
                    </div>

                    <div class="form-group">
                        <label for="form_phone">{{ __('main.form.phone') }}&nbsp;<span class="text-danger">*</span></label>
                        <input class="form-control" name="phone" id="form_phone" type="text" required>
                    </div>
                    <div class="form-group">
                        <label for="form_message">{{ __('main.form.message') }}</label>
                        <textarea class="form-control" name="message" id="form_message" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="form_security_code">{{ __('main.form.security_code') }}</label>
                        <div class="row gutters-5">
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <input type="text" name="captcha" class="form-control" id="form_security_code" placeholder="{{ __('main.form.security_code') }}" required>
                            </div>
                            <div class="col-lg-6">
                                <div class="captcha-container">
                                    <img src="{{ asset('images/captcha.png') }}" alt="Captcha" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-lg btn-primary">{{ __('main.form.send') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mt-2">
                <h4>{{ __('main.our_phone') }}:</h4>
                <span><i class="bi bi-headphones mr-1"></i><a href="tel:+998555209090">+998 55 520-90-90</a> </span>

                <h4 class="mt-3">{{ __('main.email') }}:</h4><span> <i class="bi bi-send-check mr-1"></i> <a href="mailto:info@allgood.uz">info@allgood.uz</a></span>
            </div>

            <div class="col-md-3 mt-2">
                <h4 class="ml-1">{{ __('main.our_soc_nets') }}:</h4>
                <a href="https://play.google.com/store/apps/details?id=uz.allgood">
                    <img src="{{ asset('img/Google_Play.png') }}" alt="Google Play" style="height: 70px!important; width: 200px!important;">
                </a>
                <br>
                <a href="https://apps.apple.com/uz/app/allgood/id1637811830">
                    <img src="{{ asset('img/App_Store.png') }}" alt="App Store" style="height: 70px!important; width: 200px!important;">
                </a>
            </div>

            <div class="col-md-3 mt-2">
                <h4>{{ __('main.links_to_soc_medias') }}:</h4>
                <a href="https://t.me/allgood_admin" style="font-size: 20px;"><i class="bi bi-telegram" style="color: #2FA3D9"></i> <span class="text-muted"> Telegram </span></a> <br>
                <a href="https://www.instagram.com/allgood.uz/" style="font-size: 20px;"><i class="bi bi-instagram" style="color: #ED4962"></i> <span class="text-muted"> Instagram </span></a> <br>
                <a href="https://www.facebook.com/AllGood.UZ" style="font-size: 20px;"><i class="bi bi-facebook" style="color: #4867AA"></i> <span class="text-muted"> Facebook </span></a> <br>
                <a href="https://www.youtube.com/channel/UCLM-mZufdKdN8Us_TMHO-qA" style="font-size: 20px;"><i class="bi bi-youtube mt-4" style="color: #FE0000"></i> <span class="text-muted"> Youtube </span></a> <br>
            </div>
            <div class="col-md-3 mt-2">
                <h4>{{ __('main.working_hours') }}:</h4>
                <span><i class="bi bi-clock-history mr-1"></i>{{ __('main.working_hours_description') }}</span>

                <h4 class="mt-3">{{ __('main.llc_allgood') }}</h4>
                <p>{{ __('main.llc_address') }}</p>
            </div>
        </div>

        {{-- LocalBusiness --}}
        <div itemscope itemtype="http://schema.org/LocalBusiness" class="d-none">
            <span itemprop="name">AllGood</span><br>
            <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                <span itemprop="streetAddress">{{ __('main.address_local_business') }}</span><br>
                <span itemprop="addressLocality">{{ __('main.region_local_business') }}</span><br>
                <span itemprop="addressRegion">{{ __('main.district_local_business') }}</span>
                <span itemprop="addressCountry">{{ __('main.country_local_business') }}</span>
                <span itemprop="postalCode">100057</span>
                <span itemprop="email">info@allgood.uz</span>
                <span itemprop="url">{{ url()->full() }}</span>
                <span itemprop="description"> {{ __('main.description_local_business') }} </span>
                <span itemprop="logo">https://allgood.uz/storage/settings/August2022/c5yiOdg3QlwL4czzQedQ.png</span>
            </div>
            <span itemprop="telephone">+998 78 777-90-90</span>
        </div>

        @if ($page->body)
            <div class="text-block my-5">
                {!! $page->body !!}
            </div>
        @endif

    </div>

</main>

@endsection
