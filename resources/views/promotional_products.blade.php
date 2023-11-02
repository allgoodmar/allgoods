@extends('layouts.app')

@section('seo_title', __('main.promotions'))

@section('content')

@php
    $title = __('main.promotions');
    if ($category) {
        $title .= ' - ' . $category->name;
    }
@endphp

<section class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="order-lg-2 col-lg-9 col-xl-9 main-block">

                <section class="content-block">
                    <x-top-search></x-top-search>
                </section>

                @include('partials.breadcrumbs')

                <h1 class="main-header mt-3">{{ $title }}</h1>

                @if(!$products->isEmpty())
                    <div class="products-list">
                        <div class="row">
                            @foreach($products as $product)
                                <div class="col-xl-3 col-lg-3 col-md-4 col-6">
                                    @include('partials.product_one')
                                </div>
                            @endforeach
                        </div>
                        {!! $links !!}
                    </div>

                @else
                    <div class="lead">
                        {{ __('main.no_products') }}
                    </div>
                @endif

                <div class="pb-5"></div>

            </div>
            <div class="order-lg-1 col-lg-3 col-xl-3 side-block">

                @include('partials.sidebar')

            </div>
        </div>




    </div>
</section>


@endsection
