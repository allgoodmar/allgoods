@extends('layouts.app')

@section('seo_title', $page->seo_title ?: $page->name)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')

@php
    $title = $page->short_name_text;
    if ($category) {
        $title .= ' - ' . $category->name;
    }
@endphp

@include('partials.page_top', ['title' => $title, 'bg' => $page->bg])

<div class="section">
    <div class="custom-container">

        @if(!$products->isEmpty())
            <div class="products-list">
                <div class="row shop_container">
                    @foreach($products as $product)
                        <div class="col-lg-3 col-md-4 col-6">
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


    </div>
</div>

@endsection
