@extends('layouts.app')

@section('seo_title', $page->seo_title ?: $page->name)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)
@section('body_class', 'categories-page')

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <section class="hero-banner">
        <div class="container">
            <h1>{{ $page->name }}</h1>
            <div class="content-top align-items-center d-none d-lg-block">
                <strong>{{ $page->description }}</strong>
            </div>
        </div>
    </section>

    <section class="sub-categories">
        <div class="container">
            <div class="row sub-categories-wrap">
                @foreach ($categories as $category)
                    <div class="col-lg-2 col-4 sub-categories-box__parent">
                        @include('partials.category_one')
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-bestseller-products></x-bestseller-products>

    <x-discounted-products></x-discounted-products>

    <x-brands></x-brands>

    <section class="about">
        <div class="container">
            <div class="text-block">
                {!! $page->body !!}
            </div>
        </div>
    </section>

</main>

@endsection
