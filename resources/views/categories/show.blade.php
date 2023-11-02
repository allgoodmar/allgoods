@extends('layouts.app')

@if (!empty($category->name))
    @section('seo_title', $category->seo_title ?: $category->name)
    @section('meta_description', $category->meta_description)
@else
    @if (app()->getLocale() == 'ru')
        @section('seo_title', "Купить " . Str::lower($category->name) . " в Ташкенте, цены")
        @section('meta_description', "В интернет-магазине Allgood.uz.можно купить " . Str::lower($category->name) . " в Ташкенте. Продукция " . Str::lower($category->name) . " по доступным ценам в каталоге. Заказать с доставкой по Узбекистану")
    @else
        @section('seo_title', "Toshkentda " . Str::lower($category->name) . " sotib oling, narxlar")
        @section('meta_description', "Allgood.uz internet-do'konida siz " . Str::lower($category->name) . " mahsulotlarini xarid qilishingiz mumkin. Ushbu " . Str::lower($category->name) . " ni mahsulotlari katalogda arzon narxlarda. Buyurtma bering va O'zbekiston bo'ylab yetkazib beramiz")
    @endif
@endif

{{--
@section('seo_title', $category->seo_title ?: $category->name)
@section('meta_description', $category->meta_description)

--}}

@section('meta_keywords', $category->meta_keywords)
@section('body_class', 'category-page')
@section('microdata')
    {!! $microdata !!}
@endsection

@section('content')

@php
    $siteLogo = setting('site.logo');
    $logo = $siteLogo ? Voyager::image($siteLogo) : '/img/logo.png';
    $siteTitle = setting('site.title')
@endphp

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <section class="catalog">
        <div class="container">
                @if (!empty($parentcatgo))
                    {{ $parentcatgo }}
                @endif
            <h1>{{ $category->name }}</h1>
            <div class="content-top align-items-center">
                <strong class="d-none d-lg-block">{{ __('main.products2') }}: {{ $total }}</strong>

                @if(!$products->isEmpty())
                    <button class="category-filters-switch theme-btn radius-28 theme-btn-light d-lg-none">{{ __('main.filters') }}</button>

                    <div class="dropdown dropdown-sort ml-auto">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            {{-- <span>{{ __('main.sorting') }}</span> --}}
                            <span>{!! __('main.sort.' . $sortCurrent) !!}</span>
                            <svg width="16" height="16" fill="#666">
                                <use xlink:href="#arrow"></use>
                            </svg>
                        </a>

                        <div class="dropdown-menu right">
                            @foreach($sorts as $sort)
                                <a href="javascript:;" data-value="{{ $sort }}" class="dropdown-item change-sort-dropdown-item @if($sortCurrent == $sort) active @endif">{!! __('main.sort.' . $sort) !!}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div class="row catalog-wrap">

                <!-- side-column -->
                <div class="col-lg-20 col-12">
                    <aside class="catalog-sidebar">
                        <form action="{{ $category->url }}" class="category-main-form filter-form">

                            @php
                                $categoryPricesFrom = floor((isset($categoryPrices['from']) ? (int)$categoryPrices['from'] : (int)$categoryPrices['min']) / 1000) * 1000;
                                $categoryPricesTo = ceil((isset($categoryPrices['to']) ? (int)$categoryPrices['to'] : (int)$categoryPrices['max']) / 1000) * 1000;
                                $categoryPricesMin = floor(((int)$categoryPrices['min']) / 1000) * 1000;
                                $categoryPricesMax = ceil(((int)$categoryPrices['max']) / 1000) * 1000;
                            @endphp

                            @if(!$products->isEmpty())
                                <div class="filter-form__item">
                                    <a href="{{ $category->url }}" class="theme-btn radius-6 sm mt-0 mb-3">{{ __('main.show_all_reset') }}</a>
                                    <h5>{{ __('main.price') }}</h5>
                                    <strong><span id="price-range-filter-from">{{ number_format($categoryPricesFrom, 0, '.', ' ') }}</span> - <span id="price-range-filter-to">{{ number_format($categoryPricesTo, 0, '.', ' ') }}</span></strong>
                                    <label class="range-item">
                                        <input type="range" name="price[from]"  min="{{ $categoryPricesMin }}" max="{{ $categoryPricesMax }}" value="{{ $categoryPricesFrom }}" class="range-control range-control-from" step="1000">
                                        <input type="range" name="price[to]" min="{{ $categoryPricesMin }}" max="{{ $categoryPricesMax }}" value="{{ $categoryPricesTo }}" class="range-control range-control-to" step="1000">
                                    </label>
                                    {{-- <div class="mt-4"><button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('main.form.apply') }}</button></div> --}}
                                </div>
                            @endif


                            <input type="hidden" name="sort" value="{{ $sortCurrent }}">
                            <input type="hidden" name="product_view" value="{{ $productView }}">
                            <input type="hidden" name="quantity" value="{{ $quantity }}">

                            @php
                                $maxVisibleValues = 10;
                            @endphp

                            @if(!$categoryBrands->isEmpty())
                                <div class="filter-form__item">
                                    <h5>{{ __('main.brands') }}</h5>
                                    <div class="form-group" data-target="more-container">
                                        @foreach($categoryBrands as $key => $brand)
                                            @php
                                                $isBrandActive = (!empty($brands) && is_array($brands) && in_array($brand->id, $brands)) ? true : false;
                                            @endphp
                                            <div class="custom-checkbox__item">
                                                <input type="checkbox" name="brand[]" value="{{ $brand->id }}" class="category-filter-checkbox custom-checkbox" id="brand_{{ $brand->id }}" @if($isBrandActive) checked @endif>
                                                <label class="custom-checkbox-label" for="brand_{{ $brand->id }}">{{ $brand->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($categoryBrands->count() > 5) <a href="#" data-toggle="more-btn">{{ __('main.show_more') }}</a> @endif
                                </div>
                            @endif

                            @if(!$categoryAttributes->isEmpty())
                                @foreach($categoryAttributes as $attribute)
                                    <div class="filter-form__item" id="filter-attribute-{{ $attribute->id }}">
                                        <h5>{{ $attribute->getTranslatedAttribute('name') }}</h5>
                                        <div class="form-group" data-target="more-container">
                                            @foreach($attribute->attributeValues as $key => $attributeValue)
                                                @php
                                                    $isAttrValueActive = (!empty($attributes[$attribute->id]) && is_array($attributes[$attribute->id]) && in_array($attributeValue->id, $attributes[$attribute->id])) ? true : false;
                                                @endphp
                                                <div class="custom-checkbox__item">
                                                    <input type="checkbox" class="category-filter-checkbox custom-checkbox" name="attribute[{{ $attribute->id }}][]" value="{{ $attributeValue->id }}" id="attribute_value_{{ $attributeValue->id }}" @if ($isAttrValueActive) checked @endif>
                                                    <label class="custom-checkbox-label" for="attribute_value_{{ $attributeValue->id }}">{{ $attributeValue->getTranslatedAttribute('name') }}</label>
                                                </div>
                                            @endforeach

                                            <div class="help-block"></div>
                                        </div>
                                        @if($attribute->attributeValues->count() > 3) <a href="#" data-toggle="more-btn">{{ __('main.show_more') }}</a> @endif
                                    </div>
                                @endforeach
                            @endif

                            <div class="mt-4 mb-5">
                                {{-- <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('main.show_all') }}</button> --}}
                                <a class="theme-btn radius-6 sm" href="{{ $category->url }}">{{ __('main.show_all_reset') }}</a>
                            </div>

                        </form>
                    </aside>
                </div>

                <!-- main-column -->
                <div class="col-lg-80 col-12">

                    @if (!$subcategories->isEmpty())
                        <nav class="navbar-d radius-6 d-none d-lg-block mb-4">
                            <ul class="navbar-d__list">
                                @foreach ($subcategories as $subcategory)
                                    <li class="navbar-d__item">
                                        <a href="{{ $subcategory->url }}" class="navbar-d__link">{{ $subcategory->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                        <nav class="navbar-m d-lg-none">
                            <ul class="navbar-m__list">
                                <li class="navbar-m__item">
                                    <a href="#" class="navbar-m__link" data-toggle-menu="category-menu">
                                        <svg width="30" height="30" stroke="#1a2c3c">
                                            <use xlink:href="#menu"></use>
                                        </svg>
                                        <span>{{ __('main.categories') }}</span>
                                        <svg width="18" height="18" fill="#999">
                                            <use xlink:href="#arrow"></use>
                                        </svg>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    @endif

                    <article class="catalog-content">

                        <div class="row products-wrap">
                            @forelse ($products as $product)
                                <div class="col-lg-20 col-12 product-card__parent">
                                    @include('partials.product_one_second')
                                </div>
                            @empty
                                @php
                                    $noProductsText = Helper::staticText('no_products_text')->description ?? '';
                                @endphp
                                <div class="col-12 text-center">
                                    <div class="p-4">
                                        {!! $noProductsText !!}
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        @if(!$products->isEmpty())
                            <div class="catalog-content__bottom">
                                @if ($productAllQuantity > $quantityPerPage[0])
                                <div class="visited-products d-none d-lg-flex">
                                    <strong>{{ __('main.show_per_page') }}</strong>
                                    <div class="dropdown">
                                        <a href="javascript:;" class="dropdown-toggle radius-6" data-toggle="dropdown">
                                            <span>{{ $quantity }}</span>
                                            <svg width="16" height="16" fill="#666">
                                                <use xlink:href="#arrow"></use>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu">
                                            @foreach($quantityPerPage as $value)
                                                <a href="javascript:;" data-value="{{ $value }}" class="change-per-page-dropdown-item dropdown-item @if($quantity == $value) active @endif">{{ $value }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                                {{-- <a href="#" class="theme-btn radius-6 theme-btn-light d-lg-none">Дальше</a> --}}

                                {!! $links !!}
                            </div>
                        @endif

                    </article>
                </div>
            </div>
        </div>
    </section>

    @if ($category->body)
        <section class="about">
            <div class="container customize_info">
                {!! $category->body !!}
            </div>
        </section>
    @endif

</main>

@if (!$subcategories->isEmpty())
    <div class="category-menu" data-target-menu="category-menu">
        <div class="category-menu__header">
            <button type="button" data-toggle="menu-close">
                <svg width="24" height="24" fill="#333">
                    <use xlink:href="#close"></use>
                </svg>
            </button>
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ $logo }}" alt="{{ $siteTitle }}" class="img-fluid">
                </a>
            </div>
        </div>
        <div class="category-menu__content">
            <div class="category-menu__body">
                <ul class="category-menu__list">
                    @foreach ($subcategories as $subcategory)
                        <li>
                            <a href="{{ $subcategory->url }}" class="text-uppercase">
                                <span>{{ $subcategory->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
    {{-- <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.ui.touch-punch.min.js') }}"></script>
    <script src="{{ asset('js/TweenMax.min.js') }}"></script> --}}
    <script src="{{ asset('js/confetti.js') }}"></script>
    
    <script>
        $(function(){
            let shopContainerView = localStorage.getItem('shopContainerView');
            if (!shopContainerView) {
                shopContainerView = 'list';
            }
            $('.shop_container').removeClass('list').removeClass('grid').addClass(shopContainerView);
            $('.shop_container_icon.' + shopContainerView).addClass('active').siblings().removeClass('active');


            let form = $('.category-main-form');
            $('.category-filter-checkbox').on('change', function(e){
                form.submit();
            });
            $('.change-sort-select').on('change', function(){
                let newValue = $(this).val();
                form.find('[name="sort"]').val(newValue);
                form.submit();
            });

            /* price range */
            $('.range-control-from').on('input', function(){
                let newValue = +$(this).val();
                $('#price-range-filter-from').text(newValue.toLocaleString('ru-RU'));
            });
            $('.range-control-from').on('change', function(){
                // let newValue = $(this).val();
                // form.find('[name="from"]').val(newValue);
                form.submit();
            });
            $('.range-control-to').on('input', function(){
                let newValue = +$(this).val();
                $('#price-range-filter-to').text(newValue.toLocaleString('ru-RU'));
            });
            $('.range-control-to').on('change', function(){
                // let newValue = $(this).val();
                // form.find('[name="to"]').val(newValue);
                form.submit();
            });

            $('.change-sort-dropdown-item').on('click', function(e){
                e.preventDefault();
                if ($(this).hasClass('active')) {
                    return;
                }
                // $('#change-sort-dropdown-btn').text($(this).text());
                $(this).parent().find('.active').removeClass('active');
                $(this).addClass('active');
                let newValue = $(this).data('value');
                form.find('[name="sort"]').val(newValue);
                form.submit();
            });
            $('.change-per-page-select').on('change', function(){
                let newValue = $(this).val();
                form.find('[name="quantity"]').val(newValue);
                form.submit();
            });
            $('.change-per-page-dropdown-item').on('click', function(e){
                e.preventDefault();
                if ($(this).hasClass('active')) {
                    return;
                }
                // $('#change-sort-dropdown-btn').text($(this).text());
                $(this).parent().find('.active').removeClass('active');
                $(this).addClass('active');
                let newValue = $(this).data('value');
                form.find('[name="quantity"]').val(newValue);
                form.submit();
            });
            $('.side-box-list-switch').on('click', function(e){
                e.preventDefault();
                $(this).toggleClass('active');
                let targetIdHash = $(this).attr('href');
                let target = $(targetIdHash);
                if (target.length) {
                    target.find('.category-filter-row-visibility-changable').toggleClass('d-none');
                }
            });

            $('.product-list-view-change').on('click', function(e){
                e.preventDefault();
                if ($(this).hasClass('active')) {
                    return;
                }
                $(this).parent().find('.active').removeClass('active');
                $(this).addClass('active');
                let newValue = $(this).data('product-view');
                form.find('[name="product_view"]').val(newValue);
                form.submit();
            });

            $('.category-filters-box .side-box .box-header').on('click', function(){
                let parentBox = $(this).closest('.side-box');
                if (!parentBox.length) {
                    return;
                }
                parentBox.toggleClass('active inactive');
                // let boxList = parentBox.find('.side-box-list');
                // if (boxList.length) {
                //     boxList.toggleClass('active');
                // }
            });

            $('.category-brands-block').length && $('.category-brands-block').slick({
                autoplay: true,
                // infinite: false,
                slidesToShow: 6,
                slidesToScroll: 1,
                arrows: false,
                responsive: [
                    {
                        breakpoint: 1470,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 1080,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 720,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1
                        }
                    },
                ]
            });

        }); // ready end
    </script>
@endsection
