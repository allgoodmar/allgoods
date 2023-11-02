@php
    $siteTitle = setting('site.title');
    $phone = setting('contact.phone');
    $email = setting('contact.email');
@endphp

@if(!str_contains(url()->current(), '/cart'))
<footer class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row footer-top__wrap">
                <div class="col-lg-3 footer-nav__collapse">
                    <h4 data-toggle="collapse">
                        <span>{{ __('main.about_company') }}</span>
                        <svg width="16" height="16" fill="#4d6275" class="d-lg-none">
                            <use xlink:href="#arrow"></use>
                        </svg>
                    </h4>
                    <ul class="footer-nav list-unstyled collapse">
                        @foreach ($footerMenuItems[0] as $item)
                            <li>
                                <a href="{{ $item->url }}">{{ $item->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 footer-nav__collapse">
                    <h4 data-toggle="collapse">
                        <span>{{ __('main.footer.customer_help') }}</span>
                        <svg width="16" height="16" fill="#4d6275" class="d-lg-none">
                            <use xlink:href="#arrow"></use>
                        </svg>
                    </h4>
                    <ul class="footer-nav list-unstyled collapse">
                        @foreach ($footerMenuItemsMain as $item)
                            <li>
                                <a href="{{ $item->url }}">{{ $item->name }}</a>
                            </li>
                        @endforeach
                            <li>
                                <a href="javascript:;" data-toggle="modal" data-target="#callBackFormModal">{{ __('main.feedback_form') }}</a>
                            </li>
                    </ul>
                </div>
                @include('partials.call_back_form')
                <div class="col-lg-3 d-none d-lg-block"> {{-- old: offset-lg-1 col-lg-5 d-none d-lg-block --}}
                    <h4>Скачать приложение</h4>
                    <div class="d-none d-xl-block">
                        <div class="row">
                            <div class="col-3">
                                <div class="row">
                                    <div class="col-12">
                                        <a href="https://play.google.com/store/apps/details?id=uz.allgood">
                                            <img src="{{ asset('img/Google_Play.png') }}" alt="Google Play" style="height: 50px!important; width:160px!important;">
                                        </a>
                                    </div>
                                    <div class="col-12">
                                        <a href="https://apps.apple.com/uz/app/allgood/id1637811830">
                                            <img src="{{ asset('img/App_Store.png') }}" alt="App Store" style="height: 50px!important; width:160px!important;">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6" style="margin-left: 70px!important;">
                                <div class="row">
                                    <div class="col-6">
                                        <img src="{{ asset('img/onelink.png') }}" alt="App Store" style="height: 100px!important; width:100px!important;">
                                    </div>
                                </div>
                            </div>
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
                </div>
                <div class="col-lg-3 footer-nav__collapse">
                    <h4 data-toggle="collapse">
                        <span>Рассрочка</span>
                    </h4>
                    <div class="d-none d-xl-block">
                        <div class="footer-brand-item">
                            <a href="https://allgoodnasiya.uz" target="_blank" rel="nofollow">
                                <img src="{{ asset('images/partners/anlogo.webp') }}" alt="AllGood Nasiya">
                            </a>
                        </div>
                        <div class="footer-brand-item">
                            <a href="https://orzuhavas.uz" target="_blank" rel="nofollow">
                                <img src="{{ asset('images/partners/orzuhavaslogo.webp') }}" alt="Orzu Havas">
                            </a>
                        </div>
                    </div>
                    <div class="d-lg-none text-center mx-auto">
                        <div class="footer-brand-item text-center mx-auto">
                            <a href="https://allgoodnasiya.uz" target="_blank" rel="nofollow">
                                <img src="{{ asset('images/partners/anlogo.webp') }}" alt="AllGood Nasiya">
                            </a>
                        </div>
                        <div class="footer-brand-item text-center mx-auto">
                            <a href="https://orzuhavas.uz" target="_blank" rel="nofollow">
                                <img src="{{ asset('images/partners/orzuhavaslogo.webp') }}" alt="Orzu Havas">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-brands">
                {{-- 
                <div class="footer-brand-item">
                    <img src="{{ asset('img/brands/payme.png') }}" alt="Payme">
                </div>
                <div class="footer-brand-item">
                    <img src="{{ asset('img/brands/click.png') }}" alt="Click">
                </div>
                <div class="footer-brand-item">
                    <img src="{{ asset('images/partners/alifnasiya.svg') }}" alt="alif nasiya">
                </div>
                <div class="footer-brand-item">
                    <img src="{{ asset('images/partners/intend.png') }}" alt="Intend">
                </div>

                <div class="footer-brand-item">
                    <a href="https://allgoodnasiya.uz" target="_blank" rel="nofollow">
                        <img src="{{ asset('images/partners/allgoodnasiya.png') }}" alt="AllGood Nasiya">
                    </a>
                </div>
                <div class="footer-brand-item">
                    <a href="https://orzuhavas.uz" target="_blank" rel="nofollow">
                        <img src="{{ asset('images/partners/orzuhavaslogo.webp') }}" alt="Orzu Havas">
                    </a>
                </div>
                --}}
                {{-- <div class="footer-brand-item">
                    <img src="img/brands/zoodpay.png" alt="">
                </div> --}}
            </div>
            <div class="footer-brands mt-2">
                <div class="row">
                    <div class="col-6 d-lg-none">
                        <a href="https://play.google.com/store/apps/details?id=uz.allgood" rel="nofollow">
                            <img src="{{ asset('img/Google_Play.png') }}" alt="Google Play" style="height: 50px!important; width:160px!important;">
                        </a>
                    </div>
                    <div class="col-6 d-lg-none">
                        <a href="https://apps.apple.com/uz/app/allgood/id1637811830" rel="nofollow">
                            <img src="{{ asset('img/App_Store.png') }}" alt="App Store" style="height: 50px!important; width:160px!important;">
                        </a>
                    </div>
                </div>
                <div class="row d-lg-none mt-2">
                    <div class="col-6">
                        <img src="{{ asset('img/onelink.png') }}" alt="App Store" class="text-center" style="height: 100px!important; width:100px!important;">
                    </div>
                </div>

                {{-- 
                <div class="d-none d-xl-block">
                    <div class="row">
                        <div class="col-6" style="width:200px!important;">
                            <div class="row">
                                <div class="col-12">
                                    <a href="https://play.google.com/store/apps/details?id=uz.allgood">
                                        <img src="{{ asset('img/Google_Play.png') }}" alt="Google Play" style="height: 50px!important; width:160px!important;">
                                    </a>
                                </div>
                                <div class="col-12">
                                    <a href="https://apps.apple.com/uz/app/allgood/id1637811830">
                                        <img src="{{ asset('img/App_Store.png') }}" alt="App Store" style="height: 50px!important; width:160px!important;">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-6">
                                    <img src="{{ asset('img/onelink.png') }}" alt="App Store" style="height: 100px!important; width:100px!important;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                --}}
            </div> 
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row footer-bottom__wrap">
                <div class="col-lg-4 col-sm-6">
                    <p class="copyright-text">&copy; {{ date('Y') }} {{ __('main.all_rights_reserved') }}</p>
                </div>
                <div class="col-lg-4">
                    <ul class="footer-social-list">
                        @include('partials.social_list')
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
@endif

<div class="overlay"></div>

<button type="button" id="back-to-top" class="fadeInUp">
    <svg width="26" height="26" fill="#fff">
        <use xlink:href="#arrow"></use>
    </svg>
</button>

<div class="fixed-bottom-bar d-lg-none d-none bottom_contact_form">
    <div>
        <a href="https://t.me/allgooduz" class="w-100" style="color: #2FA3D9;">
            <div class="p-3 shadow text-center" style="border-radius: 20px;">
                <span style="font-size: 20px; font-weight: bolder;">
                    <i class="bi bi-telegram"></i> Telegram
                </span>
            </div>
        </a>
    </div>
    <div class="mt-3">
        <a href="tel:{{ Helper::phone($phone) }}" class="w-100" style="color: #F88227;">
            <div class="p-3 shadow text-center" style="border-radius: 20px;">
                <span style="font-size: 20px; font-weight: bolder;">
                    <i class="bi bi-telephone-fill"></i> Позвонить
                </span>
            </div>
        </a>
    </div>
</div>

<!-- nav-bottom -->
<div class="nav-bottom d-lg-none">
    <div class="btn-items">
        <div class="btn-item">
            <a href="{{ route('home') }}" class="icon-btn current">
                <svg width="26" height="26" fill="#4d6275">
                    <use xlink:href="#main"></use>
                </svg>
                <span>{{ __('main.nav.home') }}</span>
            </a>
        </div>
        <div class="btn-item">
            <a href="{{ route('categories') }}" class="icon-btn">
                <svg width="26" height="26" fill="#4d6275">
                    <use xlink:href="#search-list"></use>
                </svg>
                <span>{{ __('main.nav.catalog') }}</span>
            </a>
        </div>
        <div class="btn-item">
            <a href="{{ route('cart.index') }}" class="icon-btn">
                <span class="badge cart_count">{{ $cartQuantity }}</span>
                <svg width="26" height="26" fill="#4d6275">
                    <use xlink:href="#cart"></use>
                </svg>
                <span>{{ __('main.cart') }}</span>
            </a>
        </div>
        <div class="btn-item">
            @guest
                <a href="{{ route('register') }}" class="icon-btn">
                    <svg width="26" height="26" fill="#4d6275">
                        <use xlink:href="#login"></use>
                    </svg>
                    <span>{{ __('main.profile') }}</span>
                </a>
            @else
                <a href="{{ route('profile.show') }}" class="icon-btn">
                    <img src="{{ auth()->user()->avatar_img }}" alt="{{ auth()->user()->name }}" width="26" height="26" class="rounded-circle">
                    <span>{{ __('main.profile') }}</span>
                </a>
            @endguest
        </div>
        {{--
        <div class="btn-item">
            <a href="{{ route('wishlist.index') }}" class="icon-btn">
                <span class="badge wishlist_count">{{ $wishlistQuantity }}</span>
                <svg width="26" height="26" fill="#4d6275">
                    <use xlink:href="#heart"></use>
                </svg>
                <span>{{ __('main.featured') }}</span>
            </a>
        </div>
         --}}
         <div class="btn-item bottom_contact_form_click">
            <a href="javascript:;" class="icon-btn">
                <i class="bi bi-telephone-fill ml-2" style="width: 28px; height: 28px; color: #4d6275;"></i>
                <span>{{ __('main.nav.contacts') }}</span>
            </a>
        </div>
    </div>
</div>

{{-- <div class="accept-cookie">
    <div class="rounded bg-light text-dark border py-2 px-3 ">
        {{ __('main.site_uses_cookie') }}
        <a href="#" class="accept-cookie-btn">{{ __('main.to_accept2') }}</a>
    </div>
</div> --}}

<!-- Contact Modal -->
<div class="modal fade" id="contact-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('contacts.send') }}" class="contact-form">
                @csrf
                <input type="hidden" name="product_id" value="">
                <div class="modal-body">
                    <h5 class="modal-title">
                        {{ __('main.form.send_request') }}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-times"></i></span>
                        </button>
                    </h5>
                    <br>
                    <div class="form-result"></div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="name"
                            placeholder="{{ __('main.form.your_name') }}" required />
                    </div>
                    <div class="form-group">
                        <input class="form-control phone-input-mask" type="text" name="phone"
                            placeholder="{{ __('main.form.phone') }}" required />
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="message" rows="4"
                            placeholder="{{ __('main.form.message') }}"></textarea>
                    </div>

                    <div class="row gutters-5 mb-4">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <input type="text" name="captcha" class="form-control"
                                placeholder="{{ __('main.form.security_code') }}" required>
                        </div>
                        <div class="col-lg-6">
                            <div class="captcha-container">
                                <img src="{{ asset('images/captcha.png') }}" alt="Captcha" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary" type="submit">
                            {{ __('main.form.send') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Phone Call Modal -->
{{-- <span class="phone-call-button" data-toggle="modal" data-target="#phone-call-modal">
    <img src="{{ asset('images/phone.svg') }}" alt="{{ __('main.callback') }}" title="{{ __('main.callback') }}"
class="img-fluid">
</span> --}}
<div class="modal fade" id="phone-call-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('contacts.send') }}" class="contact-form">
                @csrf
                <input type="hidden" name="type" value="{{ \App\Contact::TYPE_CALLBACK }}">

                <div class="modal-body">
                    <h5 class="modal-title" id="phone-call-modal-label">
                        {{ __('main.callback') }}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-times"></i></span>
                        </button>
                    </h5>
                    <br>
                    <div class="form-result"></div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="name"
                            placeholder="{{ __('main.form.your_name') }}" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control phone-input-mask" type="text" name="phone"
                            placeholder="{{ __('main.form.phone') }}" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="message"
                            placeholder="{{ __('main.what_time_should_we_call_you_back') }}" required>
                    </div>

                    <div class="row gutters-5 mb-4">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <input type="text" name="captcha" class="form-control"
                                placeholder="{{ __('main.form.security_code') }}" required>
                        </div>
                        <div class="col-lg-6">
                            <div class="captcha-container">
                                <img src="{{ asset('images/captcha.png') }}" alt="Captcha" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary" type="submit">
                            {{ __('main.form.send') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cart-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="cart-message mb-3 mt-2 text-center h4">
                    {{ __('main.product_added_to_cart') }}
                </h4>
                <div class="text-center">
                    <button type="button" class="btn btn-secondary mb-2" data-dismiss="modal">
                        {{ __('main.continue_shopping') }}
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-primary mb-2">
                        {{ __('main.go_to_cart') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div class="modal fade" id="info-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="cart-message mb-3 mt-2 text-center h4">
                    {{ __('main.information') }}
                </h4>
                <div class="text-center info-modal-content"></div>
                <br>
            </div>
        </div>
    </div>
</div>

<!-- Regions list Modal -->
<div class="modal fade" id="regions-list-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="mb-3 mt-2 text-center h4">
                    {{ __('main.choose_a_region') }}
                </h4>
                <div class="text-center">
                    <form action="{{ route('region.set') }}" method="post" class="regions-list-form">

                        @csrf

                        <div class="form-result"></div>

                        <div class="list-group regions-list-group">
                            @foreach ($regions as $region)
                            <span class="list-group-item @if ($region->id ==
                                    $currentRegionID) active disabled @endif"
                                data-region-id="{{ $region->id }}">{{ $region->short_name }}</span>
                            @endforeach
                        </div>
                        <input type="hidden" name="region_id" value="">

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
