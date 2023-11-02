@extends('layouts.app')

@section('seo_title', $page->seo_title ?: $page->name)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')

    <section class="content-block mt-0">
        <div class="container-fluid">
            <div class="row">
                <div class="order-lg-2 col-lg-9 col-xl-9 main-block">

                    <section class="content-block">
                        <x-top-search></x-top-search>
                    </section>

                    @include('partials.page_top', ['title' => $page->short_name_text, 'bg' => $page->bg])

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
