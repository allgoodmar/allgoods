@extends('layouts.app')

@section('seo_title', $page->seo_title ?: $page->name)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')

<section class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="order-lg-2 col-lg-9 col-xl-9 main-block">

                <section class="content-block">
                    <x-top-search></x-top-search>
                </section>

                @include('partials.breadcrumbs')

                <h1 class="main-header mt-3">{{ $page->name }}</h1>

                <div class="row pb-4">
                    @forelse ($publications as $publication)
                        <div class="col-lg-6 col-md-6">
                            @include('partials.news_one')
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center lead">
                                {{ __('main.no_publications') }}
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($links)
                    <div class="pb-5">
                        {!! $links !!}
                    </div>
                @endif

            </div>
            <div class="order-lg-1 col-lg-3 col-xl-3 side-block">

                @include('partials.sidebar')

            </div>
        </div>

        <div class="pb-5"></div>
    </div>
</section>


@endsection
