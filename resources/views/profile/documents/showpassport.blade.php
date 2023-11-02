@extends('layouts.app')

@section('seo_title', __('main.moiDokumenti'))

@section('content')

<main class="main">

    <div class="container py-4 py-lg-5">

        <div class="box">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4 mx-auto">
                            @if (empty($passportUser->passport_main_string))
                                <span class="badge bg-danger">Не добавлено</span>
                            @else
                                <img src="{{ asset('storage/'.$passportUser->passport_main_string) }}" alt="{{ $passportUser->passport_main_string }}" class="img-fluid">
                            @endif
                        </div>
                    </div>
                    <br>
                    <i class="bi bi-link"></i>
                    <a href="{{ asset('storage/'.$passportUser->passport_main_string) }}">Пасспорт селфи</a>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4 mx-auto">
                            @if (empty($passportUser->passport_address_string))
                                <span class="badge bg-danger">Не добавлено</span>
                            @else
                                <img src="{{ asset('storage/'.$passportUser->passport_address_string) }}" alt="{{ $passportUser->passport_address_string }}" class="img-fluid">
                            @endif
                        </div>
                    </div>
                    <br>
                    <i class="bi bi-link"></i>
                    <a href="{{ asset('storage/'.$passportUser->passport_main_string) }}">Пасспорт</a>
                </div>
            </div>
        </div>

    </div>

</main>

@endsection
