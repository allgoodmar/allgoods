<div class="top-search-block" data-aos="fade" data-aos-once="true">
    <div class="row">
        <div class="col-lg-8 col-xl-9 d-none d-lg-block">
            <form class="header-bottom-search-form ajax-search-form" action="{{ route('search') }}" data-ajax-url="{{ route('search.ajax') }}">
                <div class="input-group">
                    <input type="text" name="q" class="form-control ajax-search-form-input" placeholder="{{ __('main.search_the_catalogue') }}" autocomplete="off">
                    <div class="input-group-append">
                        <button class="btn btn-outline-light" type="submit">
                            <img src="{{ asset('images/icons/search-dark.png') }}" alt="{{ __('main.search') }}">
                        </button>
                    </div>
                </div>
                <div class="ajax-search-form-results standard-ajax-search-form-results list-group"></div>
            </form>
        </div>
        <div class="col-lg-4 col-xl-3 pt-4 pt-lg-0">
            <div id="header-bottom-icons" class="header-bottom-icons">
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-outline-light d-lg-none header-bottom-catalog-switch" data-target="#header-bottom-catalog">
                        <img src="{{ asset('images/icons/menu.png') }}" alt="{{ __('main.menu') }}">
                    </button>
                    <button type="button" class="btn btn-outline-light d-lg-none header-bottom-mobile-search-switch" data-target="#header-bottom-icons">
                        <img src="{{ asset('images/icons/search-dark.png') }}" alt="{{ __('main.search') }}">
                    </button>
                    @auth
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-light border-radius-lg-left" title="{{ __('main.profile') }}">
                            <img src="{{ asset('images/icons/account.png') }}" alt="{{ __('main.profile') }}">
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light border-radius-lg-left" title="{{ __('main.login') }}">
                            <img src="{{ asset('images/icons/account.png') }}" alt="{{ __('main.login') }}">
                        </a>
                    @endauth
                    <a href="{{ route('compare.index') }}" class="btn btn-outline-light">
                        <img src="{{ asset('images/icons/compare.png') }}" alt="{{ __('main.compare') }}">
                        {{-- <span class="sticker sticker-count compare_count">{{ $compareQuantity > 0 ? $compareQuantity : '' }}</span> --}}
                        <span class="sticker sticker-count compare_count">{{ $compareQuantity }}</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light">
                        <img src="{{ asset('images/icons/cart.png') }}" alt="{{ __('main.cart') }}">
                        {{-- <span class="sticker sticker-count cart_count">{{ $cartQuantity > 0 ? $cartQuantity : '' }}</span> --}}
                        <span class="sticker sticker-count cart_count">{{ $cartQuantity }}</span>
                    </a>
                </div>
                <form action="{{ route('search') }}" class="header-bottom-mobile-search ajax-search-form d-lg-none" id="header-bottom-mobile-search" data-ajax-url="{{ route('search.ajax') }}">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control ajax-search-form-input" placeholder="{{ __('main.search') }}" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-outline-light" type="submit">
                                <img src="{{ asset('images/icons/search-dark.png') }}" alt="{{ __('main.search') }}">
                            </button>
                        </div>
                    </div>
                    <div class="ajax-search-form-results standard-ajax-search-form-results list-group"></div>
                </form>
            </div>
        </div>
    </div>
    <div class="d-md-none text-center mt-4">
        <a class="logo d-inline-block" href="{{ route('home') }}">
            <img src="{{ $logo }}" alt="{{ setting('site.title') }}" class="img-fluid">
        </a>
    </div>
</div>
