@extends('layouts.app')

@section('seo_title', __('main.nav.search'))
@section('meta_description', '')
@section('meta_keywords', '')

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <h1>{{ __('main.search_results') }}</h1>

        <form action="{{ route('search') }}" class="search-form">

            <div class="input-group input-group-lg mb-4">
                <input type="text" name="q" class="form-control" placeholder="{{ __('main.search') }}" value="{{ $q }}">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">
                        {{ __('main.search') }}
                    </button>
                </div>
            </div>

            @if(!$products->isEmpty())

                <div class="row products-wrap">
                    @foreach ($products as $product)
                        <div class="col-lg-20 col-12 product-card__parent">
                            @include('partials.product_one_second')
                        </div>
                    @endforeach
                </div>

                {!! $links !!}
            @else
                <div class="lead">
                    {{ __('main.no_products') }}
                </div>
            @endif
        </form>


    </div>
</main>

@endsection
