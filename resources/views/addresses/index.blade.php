@extends('layouts.app')
@section('seo_title', __('main.addresses'))

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

        <h1>{{ __('main.addresses') }}</h1>

        <div class="box">

            <div class="mb-4">
                <a href="{{ route('addresses.create') }}" class="btn btn-primary">{{ __('main.add_address') }}</a>
            </div>

            @if(!$addresses->isEmpty())
                <div class="table-responsive">
                    <table class="table standard-list-table">
                        <thead>
                            <tr>
                                <th>{{ __('main.address') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($addresses as $address)
                                <tr>
                                    <td class="text-nowrap">{{ $address->address_line_1 }}</td>
                                    <td class="shrink text-right">
                                        <a href="{{ route('addresses.edit', ['address' => $address->id]) }}" class="btn btn-sm btn-success">{{ __('main.to_edit') }}</a>
                                        <form action="{{ route('addresses.destroy', ['address' => $address->id]) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('main.are_you_sure') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">{{ __('main.to_delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>
                    {{ __('main.no_addresses_added') }}
                </p>
            @endif
        </div>

    </div>

</main>


@endsection
