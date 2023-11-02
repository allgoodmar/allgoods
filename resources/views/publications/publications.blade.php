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

        <div class="row articles-wrap">

            @foreach ($publications as $publication)
                <div class="col-lg-3 col-6 article-item__parent">
                    @include('partials.publication_one')
                </div>
            @endforeach

        </div>

        <div class="mt-4">
            {!! $links !!}
        </div>
    </div>
</main>

@endsection
