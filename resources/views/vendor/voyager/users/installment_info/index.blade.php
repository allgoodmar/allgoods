@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .m-0 {
            margin: 0;
        }
        .mb-4 {
            margin-bottom: 20px;
        }
        .p-4 {
            padding: 20px;
        }
    </style>
@stop

@section('page_title', 'Данные для рассрочки')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-upload"></i>
        Данные для рассрочки
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                @include('voyager::alerts')

                <div class="panel panel-bordered">
                    <div class="panel-heading p-4">
                        <h4 class="m-0">Данные для рассрочки</h4>
                    </div>
                    <div class="panel-body">

                        <div class="my-4">
                            <a href="{{ route('voyager.users.index') }}" class="btn btn-primary">Назад</a>
                        </div>

                        <div class="my-4">
                            <div class="mb-5">ID: {{ $user->id }}</div>
                            <div class="mb-5">Имя: {{ $user->name }}</div>
                            <div class="mb-5">Телефон: {{ $user->phone_number }}</div>
                            <div class="mb-5">
                                <span>Статус рассрочки:</span>
                                @if($user->installment_data_verified)
                                    <span class="text-success">Верифицирован</span>
                                @else
                                    <span class="text-danger">Не верифицирован</span>
                                @endif
                            </div>
                        </div>

                        <div class="my-4">
                            <div class="mb-4">
                                <h5 class="mb-5">Паспорт</h5>
                                <div>
                                    <a href="{{ $user->passport_main_img }}" target="_blank">
                                        <img src="{{ $user->passport_main_img }}" alt="" style="max-width: 400px;height: auto;">
                                    </a>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h5 class="mb-5">Прописка</h5>
                                <div>
                                    <a href="{{ $user->passport_address_img }}" target="_blank">
                                        <img src="{{ $user->passport_address_img }}" alt="" style="max-width: 400px;height: auto;">
                                    </a>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h5 class="mb-5">Номер карты</h5>
                                <div>{{ $user->card_number }}</div>
                            </div>
                            <div class="mb-4">
                                <h5 class="mb-5">Срок действия</h5>
                                <div>{{ $user->card_expiry }}</div>
                            </div>
                        </div>

                        <br>

                        <form action="{{ route('voyager.users.installment_info.verify', ['user' => $user->id]) }}" method="post" class="my-4">

                            <h4>Изменить статус</h4>

                            @csrf

                            <div class="form-group">
                                <label>Статус</label>
                                @php
                                    $isVerified = old('installment_data_verified') ?? $user->installment_data_verified;
                                @endphp
                                <div>
                                    <label>
                                        <input type="radio" name="installment_data_verified" value="0" @if($isVerified == 0) checked @endif>
                                        <span>Не верифицирован</span>
                                    </label>
                                </div>
                                <div>
                                    <label>
                                        <input type="radio" name="installment_data_verified" value="1" @if($isVerified == 1) checked @endif>
                                        <span>Верифицирован</span>
                                    </label>
                                </div>
                                <div>
                                    <label>
                                        <input type="radio" name="installment_data_verified" value="2" @if($isVerified == 2) checked @endif>
                                        <span>В ожидании проверки</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="user_installment_limit">Лимит рассрочки</label>
                                <input type="text" class="form-control" id="user_installment_limit" name="installment_limit" value="{{ old('installment_limit') ?? $user->installment_limit }}">
                                @error('installment_limit')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Сохранить</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

@stop

@section('javascript')
    <script>
        $(function(){
            //
        });
    </script>
@stop
