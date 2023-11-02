@php
    $phone = setting('contact.phone');
    $email = setting('contact.email');
    $siteTitle = setting('site.title')
@endphp
{{-- @if (auth()->check() && auth()->user()->isAdmin())
<div class="py-3 px-3 text-light position-fixed"
    style="top: 0; left: 0; z-index: 10000;width: 220px;background-color: #000;">
    <div class="container-fluid">
        <a href="{{ url('admin') }}" class="text-light">Панель управления</a>
    </div>
</div>
@endif --}}

@php
        $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
        $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
    @endphp

<div class="border d-lg-none" id="DivApp">
    <div class="col-md-12 d-flex justify-content-between py-2">

        <span class="text-muted mt-2">{{ __("main.score_your_limit") }}</span>
        
        <a href="{{ route('my.calculator.index') }}" target="_blank" style="color: #ffffff; background-color: #F98329;" onclick="accpetCookie()" class="btn closeApp"><i class="bi bi-credit-card"></i> {{ __("main.get_to_know") }}</a>

        {{-- OLD FOR MOBILE
        <span class="text-muted mt-2">{{ __("main.getAppInfo") }}</span>
        @if ($Android)
        <a href="https://play.google.com/store/apps/details?id=uz.allgood" target="_blank" style="color: #ffffff; background-color: #F98329;" onclick="accpetCookie()" class="btn closeApp"><i class="bi bi-android2"></i> {{ __("main.getApp") }}</a>
        @elseif ($iPod || $iPhone || $iPad)
        <a href="https://apps.apple.com/uz/app/allgood/id1637811830" target="_blank" style="color: #ffffff; background-color: #F98329;" onclick="accpetCookie()" class="btn closeApp"><i class="bi bi-apple"></i> {{ __("main.getApp") }}</a>
        @endif
        --}}
    </div>
</div>

<div class="header-d d-none d-lg-block">
    <div class="header-d-top">
        <div class="container">
            <div class="header-d-top__wrap">
                <ul class="header-d-top__list">
                    @foreach ($headerMenuItems as $item)
                        <li>
                            <a href="{{ $item->url }}">{{ $item->name }}</a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('my.calculator.index') }}" style="color: #F98329;">{{ __('main.calculate_your_limit') }}</a>
                    </li>
                </ul>
                <ul class="header-d-top__list">
                    <li>
                        <a href="http://t.me/allgooduz" target="_blank">{{ __('main.write_to_telegram') }}</a>
                    </li>
                    <li>
                        <a href="tel:{{ Helper::phone($phone) }}" class="phone-analytics">{{ $phone }}</a>
                    </li>
                    <li>
                        @foreach ($switcher->getValues() as $item)
                            <a href="{{ $item->url }}" @if($switcher->getActive()->key == $item->key) class="current" @endif >{{ __('main.language_key_' . $item->key) }}</a>
                            @if (!$loop->last)<hr>@endif
                        @endforeach
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-d-bottom">
        <div class="container">
            <div class="header-d-bottom__wrap">
                <div class="logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ $logo }}" alt="{{ $siteTitle }}">
                    </a>
                </div>

                <a href="{{ route('categories') }}" class="theme-btn sm radius-6" id="showFullCatalog" data-toggle="catalog-menu-d">
                    <svg width="22" height="22" fill="#fff">
                        <use xlink:href="#list"></use>
                    </svg>
                    <span>{{ __('main.nav.catalog') }}</span>
                </a>

                <form action="{{ route('search') }}" id="sss" style="margin-left: 12px; padding-left:0!important;" class="search-field radius-6 top-search-form ajax-search-form ajax-search-container">
                    <label>
                        <svg width="24" height="24" stroke="#4d6275">
                            <use xlink:href="#search"></use>
                        </svg>
                        <input type="text" class="input-field-search mysearchone history-input mysearchoneNewAll" id="mysearchoneNewOne" name="q" autocomplete="off">
                        <div class="multisearchContainer">

                        </div>
                    </label>
                    <button type="submit" class="theme-btn sm search-btn">{{ __('main.search') }}</button>
                    <div class="ajax-search-results">
                        <div class="ajax-search-results-content py-4">
                            <div class="container searchBody">

                            </div>
                        </div>
                    </div>
                </form>

                <ul class="header-d-nav__list">
                    <li>
                        @guest
                            <a href="{{ route('login') }}">
                                {{-- <span class="badge">15</span> --}}
                                <svg width="24" height="24" stroke="#0b2031">
                                    <use xlink:href="#login"></use>
                                </svg>
                                <span>{{ __('main.login') }}</span>
                            </a>
                        @else
                            <a href="{{ route('profile.show') }}">
                                <img src="{{ auth()->user()->avatar_img }}" alt="{{ auth()->user()->name }}" width="24" height="24" class="rounded-circle" >
                                <span>{{ __('main.profile') }}</span>
                            </a>
                        @endguest
                    </li>
                    <li>
                        <a href="{{ route('profile.orders') }}">
                            <svg width="24" height="24" fill="#0b2031">
                                <use xlink:href="#cube"></use>
                            </svg>
                            <span>{{ __('main.orders') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wishlist.index') }}">
                            <span class="badge wishlist_count">{{ $wishlistQuantity }}</span>
                            <svg width="24" height="24" fill="#0b2031">
                                <use xlink:href="#heart"></use>
                            </svg>
                            <span>{{ __('main.featured') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cart.index') }}">
                            <span class="badge cart_count">{{ $cartQuantity }}</span>
                            <svg width="24" height="24" fill="#0b2031">
                                <use xlink:href="#cart"></use>
                            </svg>
                            <span>{{ __('main.cart') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="catalog-menu-d">
            <div class="container">
                <div class="row catalog-menu-d__wrap">
                    <div class="col-lg-20">
                        <ul class="catalog-menu-d__list">
                            @foreach ($categories as $key => $category)
                                <li>
                                    <a href="{{ $category->url }}" class="radius-6 parent-category @if($key == 0) current @endif" data-category-id="{{ $category->id }}">
                                        <span class="category-svg-icon">
                                            {!! $category->svg_icon_img !!}
                                        </span>
                                        {{-- <img src="{{ $category->micro_icon_img }}" alt="{{ $category->name }}"> --}}
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-lg-80">
                        @foreach ($categories as $key => $category)
                            <div class="catalog-menu-d__content-container @if($key == 0) active @endif"  data-category-id="{{ $category->id }}">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="catalog-menu-d__content">
                                            @if (!$category->children->isEmpty())
                                                <div class="catalog-menu-d-nav__wrap">
                                                    @foreach ($category->children as $child)
                                                        <div class="catalog-menu-d-nav">
                                                            <a href="{{ $child->url }}">{{ $child->getTranslatedAttribute('name') }}</a>
                                                            @if (!$category->children->isEmpty())
                                                                <ul class="catalog-menu-d-nav__list">
                                                                    @foreach ($child->children as $subchild)
                                                                    <li @if($loop->index > 4) style="display: none;" @endif>
                                                                        <a href="{{ $subchild->url }}">{{ $subchild->getTranslatedAttribute('name') }}</a>
                                                                    </li>
                                                                    @endforeach
                                                                    @if ($child->children->count() > 5)
                                                                        <li>
                                                                            <a href="javascript:;" class="catalog-menu-d-nav__list-switch" style="color: #F98329;">{{ __('main.ewyo') }}</a>
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    @endforeach

                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        @if (!empty($menuCategoryBanners[$category->id]))
                                            <div class="d-flex h-100 justify-content-center align-items-center">
                                                @php
                                                    $banner = $menuCategoryBanners[$category->id];
                                                @endphp
                                                @if ($banner->url)
                                                    <a href="{{ $banner->url }}" class="d-block">
                                                @endif
                                                    <img src="{{ $banner->img }}" alt="{{ $banner->name }}" class="img-fluid category-menu-promotion-img">
                                                @if ($banner->url)
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="header-m d-lg-none">
    <div class="header-m-top">
        <div class="container">
            <div class="header-m-top__wrap">
                <div class="logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ $logo }}" alt="{{ $siteTitle }}" class="img-fluid">
                    </a>
                </div>
                <div class="contacts">
                    <a href="tel:{{ Helper::phone($phone) }}" class="phone-link">{{ $phone }}</a>
                    <div class="dropdown dropdown-lang">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span>{{ $switcher->getActive()->key }}</span>
                            <svg width="14" height="14" fill="#4d6275">
                                <use xlink:href="#arrow"></use>
                            </svg>
                        </a>
                        <div class="dropdown-menu right">
                            @foreach ($switcher->getValues() as $item)
                                <a href="{{ $item->url }}" class="dropdown-item text-uppercase">{{ $item->key }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-m-bottom">
        <div class="container">
            <div class="header-m-bottom__wrap">
                <a href="#" class="theme-btn radius-6 px-2" style="width: 400px;" data-toggle-menu="catalog-menu-m">
                    <svg width="22" height="22" fill="#fff">
                        <use xlink:href="#list"></use>
                    </svg>
                    {{ __('main.nav.catalog') }}
                </a>
                <form action="{{ route('search') }}" id="sss2" class="search-field radius-6 ajax-search-form ajax-search-container">
                    <label>
                        <svg width="24" height="24" stroke="#4d6275">
                            <use xlink:href="#search"></use>
                        </svg>
                        <input type="text" class="input-field-search mysearchone history-input mysearchoneNewAll" id="mysearchtwoNewTwo" name="q" autocomplete="off">
                    </label>
                    <div class="ajax-search-results">
                        <div class="ajax-search-results-content py-4">
                            <div class="container searchBody">

                            </div>
                        </div>
                    </div>
                </form>
                {{-- <ul class="header-m-nav__list">
                    <li>
                        <a href="#">
                            <svg width="24" height="24" stroke="#0b2031">
                                <use xlink:href="#help"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg width="24" height="24" stroke="#0b2031">
                                <use xlink:href="#bell"></use>
                            </svg>
                        </a>
                    </li>
                </ul> --}}
            </div>
        </div>
    </div>
    <div class="multisearchContainer">

    </div>
</div>

<div class="catalog-menu-m" data-target-menu="catalog-menu-m">
    <div class="catalog-menu-m__header">
        <button type="button" data-toggle="menu-close">
            <span>&times;</span>
            {{-- <svg width="24" height="24" fill="#333">
                <use xlink:href="#close"></use>
            </svg> --}}
        </button>
        <div class="logo">
            <a href="{{ route('home') }}">
                <img src="{{ $logo }}" alt="{{ $siteTitle }}" class="img-fluid">
            </a>
        </div>
    </div>
    <div class="catalog-menu-m__content">
        <div class="catalog-menu-m__body">
            <ul class="catalog-menu-m__list">
                @foreach ($categories as $category)
                    <li class="py-1">
                        <a href="{{ $category->url }}">
                            <span class="category-svg-icon">
                                {!! $category->svg_icon_img !!}
                            </span>
                            {{-- <img src="{{ $category->micro_icon_img }}" alt="{{ $category->name }}" class="mr-1"> --}}
                            <span>{{ $category->name }}</span>
                            @if (!$category->children->isEmpty())
                                <span class="show-subcategories-m" data-category-id="{{ $category->id }}">
                                    <svg width="20" height="20" fill="#f98329">
                                        <use xlink:href="#arrow"></use>
                                    </svg>
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="header-swiper-container" 
    style="overflow: hidden!important; 
    display: flex!important;
    justify-content: center!important;
    text-align: center!important;" 
    id="scroll-tags">
    <ul class="swiper-wrapper" style="display: flex!important;
    justify-content: center!important;
    text-align: center!important;">
        <li class="swiper-slide mr-2 text-dark halal_bnpl ml-2 shadow" style="background-color: white!important; border: 1px solid #F4F5F5;"><a href="{{ route('sale') }}"> <i class="bi bi-percent"></i> {{ __("main.sale") }}</a></li>
        @php
            $counter = 0;
        @endphp

        @foreach ($categories as $key => $category)
            @if ($counter <= 6)
                {{-- 
                    @if (!$category->children->isEmpty())
                        @foreach ($category->children as $child) 
                --}}
                            <li class="swiper-slide mr-2"> <a href="{{ $category->url }}"> {!! $category->svg_icon_img !!} {{ $category->name }}</a></li>
                {{-- @endforeach
                    @endif  --}}
            @endif
            @php
                $counter++;
            @endphp
        @endforeach
        <li class="swiper-slide mr-2" data-toggle="catalog-menu-d"><a href="javascript:;">{{ __("main.ewyo") }}</a></li>
    </ul>
</div>
  

@foreach ($categories as $category)
    @if (!$category->children->isEmpty())
        <div class="subcategories-m" data-category-id="{{ $category->id }}">
            <div class="catalog-menu-m__content">
                <div class="catalog-menu-m__body">
                    <ul class="catalog-menu-m__list">
                        <li>
                            <a href="javascript:;" class="close-subcategories-m" data-category-id="{{ $category->id }}">
                                <strong>
                                    &larr; {{ $category->name }}
                                </strong>
                            </a>
                        </li>
                        @foreach ($category->children as $subcategory)
                            <li class="py-1">
                                <a href="{{ $subcategory->url }}" class="">
                                    <span>{{ $subcategory->name }}</span>
                                    @if (!$subcategory->children->isEmpty())
                                        <span class="show-subcategories-m" data-category-id="{{ $subcategory->id }}">
                                            <svg width="20" height="20" fill="#f98329">
                                                <use xlink:href="#arrow"></use>
                                            </svg>
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @foreach ($category->children as $subcategory)
            @if (!$subcategory->children->isEmpty())
                <div class="subcategories-m" data-category-id="{{ $subcategory->id }}">
                    <div class="catalog-menu-m__content">
                        <div class="catalog-menu-m__body">
                            <ul class="catalog-menu-m__list">
                                <li>
                                    <a href="javascript:;" class="close-subcategories-m" data-category-id="{{ $subcategory->id }}">
                                        <strong>
                                            &larr; {{ $subcategory->name }}
                                        </strong>
                                    </a>
                                </li>
                                @foreach ($subcategory->children as $subsubcategory)
                                    <li class="py-1">
                                        <a href="{{ $subsubcategory->url }}" class="">
                                            <span>{{ $subsubcategory->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
@endforeach
