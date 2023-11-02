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

                    <h4>{{ __('main.write_your_reviews_and_wishes') }}</h4>
                    <form action="{{ route('reviews.store') }}" method="post" class="review-form">

                        @csrf

                        <input type="hidden" name="reviewable_id" value="1">
                        <input type="hidden" name="reviewable_type" value="page">
                        <input type="hidden" name="rating" value="5">

                        <div class="form-group">
                            <label for="review_name" >{{ __('main.form.your_name') }}</label>
                            <input type="text" name="name" id="review_name" class="form-control" value="@auth{{ auth()->user()->name }}@endauth" required>
                        </div>

                        <div class="form-group">
                            <label for="review_body">{{ __('main.form.message') }}</label>
                            <textarea name="body" id="review_body" class="form-control" required></textarea>
                        </div>

                        <div class="row gutters-5 mb-4">
                            <div class="col-xl-3 col-lg-6 mb-3 mb-lg-0">
                                <input type="text" name="captcha" class="form-control" placeholder="{{ __('main.form.security_code') }}" required>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="captcha-container">
                                    <img src="{{ asset('images/captcha.png') }}" alt="Captcha" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('main.form.send') }}</button>
                        </div>

                    </form>

                    @if(!$reviews->isEmpty())
                        <div class="standard-reviews-list mt-5 mb-4">
                            <h2 class="mb-3">{{ __('main.reviews') }}</h2>
                            @foreach($reviews as $review)
                                <div class="standard-review mb-4">
                                    <div class="standard-review-info mb-2">
                                        <small class="d-inline-block px-2 py-1 rounded bg-light text-dark mr-2">{{ Helper::formatDate($review->created_at) }}</small>
                                        <span class="review-author">{{ $review->name }}</span>
                                    </div>
                                    <div class="standard-review-message text-gray">
                                        {{ $review->body }}
                                    </div>
                                    <hr>
                                </div>
                            @endforeach

                            {!! $links !!}
                        </div>
                    @else
                        <div class="py-4">
                            {{ __('main.no_reviews') }}
                        </div>
                    @endif

                    <div class="text-block pb-5">
                        {!! $page->body !!}
                    </div>

                </div>
                <div class="order-lg-1 col-lg-3 col-xl-3 side-block">

                    @include('partials.sidebar')

                </div>
            </div>
        </div>
    </section>

    <x-principles></x-principles>

@endsection
