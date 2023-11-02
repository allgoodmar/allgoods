@extends('layouts.app')
@section('seo_title', __('main.edit_address'))

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    <div class="container py-4 py-lg-5">

        <div class="box mb-5">

            <h1 class="main-header">{{ __('main.edit_address') }}</h1>

            <form action="{{ route('addresses.update', [$address->id]) }}" method="post" enctype="multipart/form-data">

                @method('PUT')

                @include('addresses._form')

            </form>


        </div>

    </div>

</main>


@endsection
