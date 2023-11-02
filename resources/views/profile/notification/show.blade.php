@extends('layouts.app')

@section('seo_title', __('main.notifications'))

@section('content')

<section class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="order-lg-2 col-lg-9 col-xl-9 main-block">

                <section class="content-block">
                    <x-top-search></x-top-search>
                </section>

                @include('partials.breadcrumbs')

                <h1 class="main-header mt-3">{{ __('main.notifications') }}</h1>

                @if(Session::has('message'))
                    <div class="alert alert-success">
                        {{ Session::get('message') }}
                    </div>
                @endif

                <div class="row">

                    <div class="order-md-2 col-md-8 col-lg-9 mb-4 mb-md-0">

                        <div class="box">

                            <div class="table-responsive">
                                <table class="table standard-list-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('main.date') }}</th>
                                            <th>{{ __('main.message') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-nowrap">{{ $notification->created_at->format('d-m-Y H:i') }}</td>
                                            <td>{{ $notification->message }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>

                    <div class="order-md-1 col-md-4 col-lg-3">
                        @include('partials.sidebar_profile')
                    </div>

                </div>

                <div class="pb-5"></div>

            </div>
            <div class="order-lg-1 col-lg-3 col-xl-3 side-block">

                @include('partials.sidebar')

            </div>
        </div>



    </div>
</section>

@endsection
