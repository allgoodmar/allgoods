@extends('layouts.app')

@section('seo_title', $page->seo_title ?: $page->name)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <h1>{{ $page->name }}</h1>

        @if(!$brands->isEmpty())
            <div class="brands-list row">
                @foreach($brands as $brand)
                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <div class="category-brand-one mb-4 mx-auto">
                            <a href="{{ $brand->url }}" class="d-block text-center" title="{{ $brand->name }}">
                                <img src="{{ $brand->img }}" alt="{{ $brand->name }}" class="img-fluid">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="lead">
                {{ __('main.no_brands') }}
            </div>
        @endif



    </div>
</main>

@endsection
