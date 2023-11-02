@extends('layouts.app')

@section('seo_title', $publication->seo_title ?: $publication->name)
@section('meta_description', $publication->meta_description)
@section('meta_keywords', $publication->meta_keywords)
{{-- @section('body_class', 'no-sidebar-page') --}}

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

	<div class="container py-4 py-lg-5">

        <h1>{{ $publication->name }}</h1>

        @can('edit', $publication->getModel())
            <div class="my-4">
                <a href="{{ url('admin/publications/' . $publication->id . '/edit') }}" class="btn btn-lg btn-primary"
                    target="_blank">Редактировать (ID: {{ $publication->id }})</a>
            </div>
        @endcan

        <div class="my-3">
            <span class="publication-date d-inline-block text-dark">{{ Helper::formatDate($publication->getModel()->created_at, true) }}</span>
            {{-- <span class="page-views"><i class="fa fa-eye"></i>&nbsp;{{ $publication->views }}</span> --}}
        </div>

        @if($publication->video_code)
            <div class="fit-big-videos mb-4">
                {!! $publication->video_code !!}
            </div>
        @endif

        <div class="text-block pb-5">
            {{-- @if($publication->image)
                <img src="{{ $publication->img }}" class="img-fluid float-left mr-3 mb-3" alt="{{ $publication->name }}">
            @endif --}}
            {!! $publication->body !!}
        </div>

        @if($publication->file)
            <div class="pb-4">
                <a href="{{ Helper::getFileUrl($publication->file) }}" class="text-dark" download="{{ Helper::getFileOriginalName($publication->file) }}">{{ __('main.to_download') }} {{ $publication->file_name }}</a>
            </div>
        @endif

    </div>
</ma>

@endsection
