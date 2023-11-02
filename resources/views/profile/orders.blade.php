@extends('layouts.app')
@section('seo_title', __('main.orders'))
@section('meta_robot', 'noindex, nofollow')

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    @include('partials.alerts')

    <div class="container py-4 py-lg-5">

        <div class="row">
            <div class="col-lg-9 order-lg-2 mb-5 mb-lg-0">

                <h1>{{ __('main.orders') }}</h1>

                <div class="box">

                    @if(!$orders->isEmpty())
                        @foreach($orders as $order)
                            <div class="standard-shadow mb-4 rounded">
                                <div class="p-3 bg-light">
                                    <a href="{{ $order->url }}" class="d-block">
                                        <div class="row">
                                            <div class="col-lg-6 mb-3 mb-lg-0">
                                                <h5 class="mb-1">
                                                    {{ __('main.order') }} # {{ $order->id }}
                                                </h5>
                                                <div>
                                                    <small>{{ Helper::formatDate($order->created_at, true) }}</small>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 text-lg-right">
                                                <h5 class="mb-1">{{ Helper::formatPrice($order->total) }}</h5>
                                                <div>
                                                    <small>{{ $order->status_title }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="p-3 bg-white">
                                    @foreach ($order->orderItems as $item)
                                        @php
                                            $product = $item->product;
                                        @endphp
                                        <div class="row align-items-center mb-3">
                                            @if($product)
                                                <div class="col-auto">
                                                    <img src="{{ $product->micro_img }}" alt="{{ $item->name }}" class="img-fluid">
                                                </div>
                                            @else
                                                <div class="col-auto"></div>
                                            @endif
                                            <div class="col">
                                                {{ $item->name }}
                                            </div>
                                            <div class="col-auto text-right">
                                                x {{ $item->quantity }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>
                            {{ __('main.no_orders') }}
                        </p>
                    @endif

                    {{ $orders->links() }}
                </div>
            </div>
            <div class="col-lg-3 order-lg-1">
                @php
                    $user = auth()->user();
                @endphp
                <div class="text-center">
                    <div class="mb-4 profile-img mx-auto">
                        <img src="{{ $user->avatar_img }}" alt="{{ $user->name }}" class="rounded-circle">
                    </div>
                    <h3>
                        {{ $user->name }}
                    </h3>
                </div>
            </div>
        </div>



    </div>

    {{-- <div class="container py-4 py-lg-5">

        <div class="mb-5 d-none d-lg-block">
            <a href="{{ route('profile.show') }}">
                <strong> &larr; {{ __('main.profile') }}</strong>
            </a>
        </div>

        <h1>{{ __('main.orders') }}</h1>

        <div class="box">

            @if(!$orders->isEmpty())
                <h3 class="box-header">{{ __('main.orders') }}</h3>
                <div class="table-responsive">
                    <table class="table standard-list-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('main.date') }}</th>
                                <th>{{ __('main.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td class="text-nowrap">{{ $order->created_at->format('d-m-Y') }}</td>
                                    <td class="text-nowrap">{{ $order->status_title }}</td>
                                    <td class="shrink text-right">
                                        <a href="{{ $order->url }}" class="btn btn-primary">{{ __('main.to_show') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>
                    {{ __('main.no_orders') }}
                </p>
            @endif

            {{ $orders->links() }}
        </div>

    </div> --}}

</main>


@endsection
