@extends('layouts.app')

@section('seo_title', __('main.notifications'))

@section('content')

<main class="main">

    <section class="content-header">
        <div class="container">
            @include('partials.breadcrumbs')
        </div>
    </section>

    @include('partials.alerts')

    <div class="container py-4 py-lg-5">

        <div class="mb-5 d-none d-lg-block">
            <a href="{{ route('profile.show') }}">
                <strong> &larr; {{ __('main.profile') }}</strong>
            </a>
        </div>

        <h1>{{ __('main.notifications') }}</h1>

        <div class="box">

            @if(!$notifications->isEmpty())
                {{-- <h3 class="box-header">{{ __('main.notifications') }}</h3> --}}
                <div class="table-responsive">
                    <table class="table standard-list-table">
                        <thead>
                            <tr>
                                <th style="width: 1%">{{ __('main.date') }}</th>
                                <th>{{ __('main.message') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td><span class="text-nowrap">{{ $notification->created_at->format('d.m.Y') }}</span> <i>{{ $notification->created_at->format('H:i') }}</i></td>
                                    <td>{{ $notification->message }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>
                    {{ __('main.no_notifications') }}
                </p>
            @endif

            {{ $notifications->links() }}
        </div>

    </div>

</main>

@endsection
