@extends('layouts.app')

@section('seo_title', 'Seller Account')
@section('meta_description', 'Seller Account')
@section('meta_keywords', 'Seller Account')

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    {{-- class="d-none d-xl-block" for section --}}
    <section style="padding-bottom: 10px; width: 100%!important;">
        <div class="container">
            <div class="row border shadow">
                {{-- 
                <div style="width: 100%!important; height:200px;">
                    <img src="{{ asset('images/banner.png') }}" alt="banner" alt="image_banner" class="img-fluid shadow" style="width: 100%!important; height:200px;">
                </div>
                --}}
                <div class="col-md-12">
                    <div class="row px-2">
                        <div class="col-md-1 p-2" style="
                        display: flex;
                        justify-content: center;
                        ">
                            <img src="{{ asset('images/avatar.jpg') }}" alt="logo" class="img-fluid rounded" style="
                            text-align: center;
                            margin: auto;
                            display: block;
                            ">
                        </div>
                        <div class="col-md-11 py-2">
                            <span class="text-muted">{{ $ordersCount }} заказов</span> <i class="bi bi-star-fill text-warning"></i> 
                            {{-- <span>(48 отзывов)</span> --}}
                            <h4 class="my-1">{{ $seller->company_name }}</h4>
                            <span class="text-muted">Продавец на <span style="color: #F98329;"> AllGood </span> с {{ date('d.m.Y', strtotime($seller->created_at)) }} г.</span>
                            <br>
                            <span>{{ $seller->company_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <h1>Продукция {{ $seller->company_name }}</h1>

        {{-- FILTER SORT --}}
        <div class="content-top align-items-center">
            <strong class="d-none d-lg-block">{{ __('main.products2') }}: {{ $productsCount }}</strong>

            @if(!$products->isEmpty())
                <button class="category-filters-switch theme-btn radius-28 theme-btn-light d-lg-none">{{ __('main.categories') }}</button>
                {{--
                <div class="dropdown dropdown-sort ml-auto">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        {{-- <span>{{ __('main.sorting') }}</span>
                        <span>{!! __('main.sort.' . $sortCurrent) !!}</span> --}}
                        {{--<svg width="16" height="16" fill="#666">
                            <use xlink:href="#arrow"></use>
                        </svg>
                    </a>

                    
                        <div class="dropdown-menu right">
                            @foreach($sorts as $sort)
                                <a href="javascript:;" data-value="{{ $sort }}" class="dropdown-item change-sort-dropdown-item @if($sortCurrent == $sort) active @endif">{!! __('main.sort.' . $sort) !!}</a>
                            @endforeach 
                        </div>
                    </div>
                --}}
            @endif
        </div>
        {{-- FILTER SORT ENDS

        <form action="{{ route('sale') }}" class="sale-page-main-form">
            <input type="hidden" name="sort" value="{{ $sortCurrent }}">
        </form>
--}}
        <!-- side-column -->
        <div class="row catalog-wrap">
            
            {{-- 
                <div class="col-lg-20 col-12">
                    <aside class="catalog-sidebar">
                        <h4>{{ __('main.categories') }}</h4>
                        asdas
                    </aside>
                </div>
            --}}
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
                        {{-- link --}}
                    </div>

                @else
                    <div class="text-center lead">
                        {{ __('main.no_products') }}
                    </div>
                @endif
            </div>

    </div>
</main>

@endsection

@section('scripts')
    <script>
        $(function(){
            let form = $('.sale-page-main-form');
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
