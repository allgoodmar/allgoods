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
        <div class="text-block">
            {!! $page->body !!}
        </div>
    </div>

	<div class="container">
        @can('edit', $page->getModel())
            <div class="my-4">
                <a href="{{ url('admin/pages/' . $page->id . '/edit') }}" class="btn btn-lg btn-primary"
                    target="_blank">Редактировать (ID: {{ $page->id }})</a>
            </div>
        @endcan
    </div>

</main>

@endsection
