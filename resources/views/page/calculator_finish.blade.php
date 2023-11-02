@extends('layouts.app')


@section('seo_title', __("main.calculate_your_limit"))
{{-- 
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)
--}}

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <div class="col-md-6 mx-auto p-2 shadow mt-1" style="border-radius: 24px;">
            <p>
                {!! __("main.calculator_finish_text") !!}
            </p>
        </div>

    </div>

</main>
@endsection
