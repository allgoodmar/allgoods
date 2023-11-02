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

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <h1>{{ $title }}</h1>

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
</main>

@endsection
