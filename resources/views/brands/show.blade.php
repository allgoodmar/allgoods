@extends('layouts.app')

@if (!empty($brand->seo_title) || !empty($brand->meta_description))
    @section('seo_title', $brand->seo_title ?: $brand->name)
    @section('meta_description', $brand->meta_description)
@else
    @if (app()->getLocale() == 'ru')
        @section('seo_title', "Купить " . strtoupper($brand->name) . " в Ташкенте, цены ")
        @section('meta_description', "В интернет-магазине Allgood.uz.можно купить " . strtoupper($brand->name) . " в Ташкенте. Продукция " . strtoupper($brand->name) . " по доступным ценам в каталоге. Заказать с доставкой по Узбекистану")
    @else
        @section('seo_title', "Toshkentda " . strtoupper($brand->name) . " sotib oling, narxlar")
        @section('meta_description', "Allgood.uz internet-do'konida siz " . strtoupper($brand->name) . " mahsulotlarini xarid qilishingiz mumkin. Ushbu " . strtoupper($brand->name) . " ni mahsulotlari katalogda arzon narxlarda. Buyurtma bering va O'zbekiston bo'ylab yetkazib beramiz")
    @endif
@endif

{{-- @section('seo_title', $brand->seo_title ?: $brand->name) --}}
{{-- @section('meta_description', $brand->meta_description) --}}
@section('meta_keywords', $brand->meta_keywords)

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <h1>{{ $brand->name }}</h1>

        <div class="content-top align-items-center">
            <strong class="d-none d-lg-block">{{ __('main.products2') }}: {{ $productAllQuantity }}</strong>

            @if(!$products->isEmpty())
                <button class="category-filters-switch theme-btn radius-28 theme-btn-light d-lg-none">{{ __('main.categories') }}</button>

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

        <form action="{{ route('brands.show', ['brand'=>$brand->id, 'slug'=>$brand->slug,]) }}" class="brand-page-main-form">
            <input type="hidden" name="sort" value="{{ $sortCurrent }}">
        </form>

        <div class="row catalog-wrap">

            <!-- side-column -->
            <div class="col-lg-20 col-12">
                <aside class="catalog-sidebar">
                    <h4>{{ __('main.categories') }}</h4>
                    @foreach ($brandCategories as $key => $value)
                            <a href="{{ route('category.individual', ['brand_id'=>$brand->id, 'category_id'=>$value->id]) }}" class="mb-2 svgColor">
                                {!! $value->svg_icon_img !!} {{ $value->name }}</a>
                            <br>
                    @endforeach
                </aside>
            </div>

            <div class="col-lg-80 col-12">
                @if(!$products->isEmpty())

                <div class="row products-wrap">
                    @foreach ($products as $product)
                        <div class="col-lg-20 col-12 product-card__parent">
                            @include('partials.product_one_second')
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {!! $links !!}
                </div>

                @else
                    <div class="text-center lead">
                        {{ __('main.no_products') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($brand->body)
        <section class="about">
            <div class="container">
                {!! $brand->body !!}
            </div>
        </section>
    @endif
</main>

@endsection


@section('scripts')
    <script src="{{ asset('js/confetti.js') }}"></script>
    <script>
        $(function(){
            let form = $('.brand-page-main-form');
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
        })
    </script>
@endsection

@section('styles')
    <style>
        .svgColor svg path{
            fill: #f98329!important;
        }
    </style>
@endsection