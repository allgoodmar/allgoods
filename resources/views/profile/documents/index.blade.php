@extends('layouts.app')

@section('seo_title', __('main.moiDokumenti'))

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

        <h1>{{ __('main.moiDokumenti') }}
            @if ($process < 3)
                <span class="text-danger">{{ $process }}/4</span>
            @else
                <span class="text-success">{{ $process }}/4</span>
            @endif
        </h1>

        <div class="box">
            <div class="row">
                <div class="col-md-8 mt-3">
                    <h3>Персональные Данные для Рассрочки</h3>
                    <hr>
                    <form action="{{ route('profile.documents.documentsUpdatePassport') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        {{-- NEW PREVIEW --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="pre_col shadow border-0">
                                    @if (empty($checkMainImage))
                                    <div class="inp_img_main" id="divImageMediaPreviewSelfie">
                                        <img id="uploaded__img">
                                    </div>
                                    @else
                                    <div class="inp_img_main" id="divImageMediaPreviewSelfie">
                                        <img id="uploaded__img" src="{{ asset('storage/'.$checkMainImage->path) }}">
                                    </div>
                                    @endif

                                    <div class="select__file">
                                        <h4 class="upload_info">
                                            Сельфи с паспортом
                                        </h4>
                                        <div class="for_upload" onchange="preview()">
                                            <label for="ImageMediasSelfie">
                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                <p class="m-0">загрузить</p>
                                            </label>
                                            <input type="file" name="passport_main_string" id="ImageMediasSelfie"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="pre_col shadow border-0">
                                    @if (empty($checkAddressImage))
                                    <div class="inp_img_main" id="divImageMediaPreviewAddress">
                                        <img id="uploaded__img">
                                    </div>
                                    @else
                                    <div class="inp_img_main" id="divImageMediaPreviewAddress">
                                        <img id="uploaded__img" src="{{ asset('storage/'.$checkAddressImage->path) }}">
                                    </div>
                                    @endif

                                    <div class="select__file">
                                        <h4 class="upload_info">
                                            Лицевая сторона паспорта
                                        </h4>
                                        <div class="for_upload" onchange="preview()">
                                            <label for="ImageMediasAddress">
                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                <p class="m-0">загрузить</p>
                                            </label>
                                            <input type="file" name="passport_address_string" id="ImageMediasAddress"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mt-4">
                                <div class="pre_col shadow border-0">
                                    @if (empty($checkAdditionalImage))
                                    <div class="inp_img_main" id="divImageMediaPreviewAdditional">
                                        <img id="uploaded__img">
                                    </div>
                                    @else
                                    <div class="inp_img_main" id="divImageMediaPreviewAdditional">
                                        <img id="uploaded__img" src="{{ asset('storage/'.$checkAdditionalImage->path) }}">
                                    </div>
                                    @endif

                                    <div class="select__file">
                                        <h4 class="upload_info">
                                            Задняя сторона паспорта
                                        </h4>
                                        <div class="for_upload" onchange="preview()">
                                            <label for="ImageMediasAdditional">
                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                <p class="m-0">загрузить</p>
                                            </label>
                                            <input type="file" name="passport_additional_string" id="ImageMediasAdditional"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mt-4">
                                <div class="pre_col shadow border-0">
                                    @if (empty($checkPlasticCardImage))
                                    <div class="inp_img_main" id="PlasticCardImageDiv">
                                        <img id="uploaded__img">
                                    </div>
                                    @else
                                    <div class="inp_img_main" id="PlasticCardImageDiv">
                                        <img id="uploaded__img" src="{{ asset('storage/'.$checkPlasticCardImage->path) }}">
                                    </div>
                                    @endif

                                    <div class="select__file">
                                        <h4 class="upload_info">
                                            Фото кредит карты
                                        </h4>
                                        <div class="for_upload" onchange="preview()">
                                            <label for="PlasticCardImage">
                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                <p class="m-0">загрузить</p>
                                            </label>
                                            <input type="file" name="plastic_card_string" id="PlasticCardImage"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12 mx-auto">
                                <button type="submit" class="btn btn-success">Сохранить</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 mt-3">
                    <h3>Карта платежа
                        <span class="text-muted" style="font-size: 14px!important; cursor: pointer;" @if(!empty($user->card_number) && !empty($user->card_expiry)) id="show_card_button" @endif> <i class="bi bi-pencil"></i>
                            @if(!empty($user->card_number) && !empty($user->card_expiry)) Изменить @else Добавить @endif
                        </span>
                    </h3>

                    <hr>

                    <div class="wrapper mt-3 @if(!empty($user->card_number) && !empty($user->card_expiry)) d-block @else d-none @endif">
                        <div class="cc mastercard">
                            <svg width="295" height="87">
                                <path d="M 0 0 C 50 50 250 0 300 87"></path>
                            </svg>
                            <div class="container">
                                <div class="type">
                                    Allgood
                                </div>
                            </div>
                            <div class="holder">
                                <span class="name">{{ $user->name }}</span>
                                <span class="number">
                                    @if (!empty($user->card_number))
                                    &#x2022;&#x2022;&#x2022;&#x2022; &#x2022;&#x2022;&#x2022;&#x2022; &#x2022;&#x2022;&#x2022;&#x2022; {{ substr ($user->card_number, -4) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div id="show_card_form" class="@if(empty($user->card_number) || empty($user->card_expiry)) d-block @else d-none @endif">
                        <form action="{{ route('profile.documents.documentsUpdateCard') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row form-group mt-3">
                                <div class="col-12">
                                    <h4 class="mb-2">{{ __('main.your_card') }}
                                        @if(empty($user->card_number) || empty($user->card_expiry))
                                            <span class="badge bg-danger text-center" style="font-size: 12px!important;">Не добавлено</span>
                                        @endif
                                    </h4>
                                    <input type="text" name="card_number" class="form-control card2-input-mask" value="@if(!empty($user->card_number) && !empty($user->card_expiry)) {{ $user->card_number }} @endif">
                                </div>
                                <div class="col-12">
                                    <h4 class="mb-2">{{ __('main.your_card_val') }}</h4>
                                    <input type="text" name="card_validation_date" class="form-control card-available2-input-mask" value="@if(!empty($user->card_number) && !empty($user->card_expiry)) {{ $user->card_expiry }} @endif">
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-success">Сохранить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</main>

@endsection

@section('scripts')

    <script>
        $( function(){
            $("#show_card_button").on('click', function(){
                $("#show_card_form").toggleClass('d-block d-none');
            });

            $("#ImageMediasSelfie").change(function () {
                if (typeof (FileReader) != "undefined") {
                    var dvPreview = $("#divImageMediaPreviewSelfie");
                    dvPreview.html("");
                    $($(this)[0].files).each(function () {
                        var file = $(this);
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var img = $("<img />");
                                img.attr("style", "width: 100%; height:100%; padding: 10px");
                                img.attr("src", e.target.result);
                                dvPreview.append(img);
                            }
                            reader.readAsDataURL(file[0]);
                    });
                } else {
                    alert("This browser does not support HTML5 FileReader.");
                }
            });

            $("#ImageMediasAddress").change(function () {
                if (typeof (FileReader) != "undefined") {
                    var dvPreview = $("#divImageMediaPreviewAddress");
                    dvPreview.html("");
                    $($(this)[0].files).each(function () {
                        var file = $(this);
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var img = $("<img />");
                                img.attr("style", "width: 100%; height:100%; padding: 10px");
                                img.attr("src", e.target.result);
                                dvPreview.append(img);
                            }
                            reader.readAsDataURL(file[0]);
                    });
                } else {
                    alert("This browser does not support HTML5 FileReader.");
                }
            });

            $("#ImageMediasAdditional").change(function () {
                if (typeof (FileReader) != "undefined") {
                    var dvPreview = $("#divImageMediaPreviewAdditional");
                    dvPreview.html("");
                    $($(this)[0].files).each(function () {
                        var file = $(this);
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var img = $("<img />");
                                img.attr("style", "width: 100%; height:100%; padding: 10px");
                                img.attr("src", e.target.result);
                                dvPreview.append(img);
                            }
                            reader.readAsDataURL(file[0]);
                    });
                } else {
                    alert("This browser does not support HTML5 FileReader.");
                }
            });

            $("#PlasticCardImage").change(function () {
                if (typeof (FileReader) != "undefined") {
                    var dvPreview = $("#PlasticCardImageDiv");
                    dvPreview.html("");
                    $($(this)[0].files).each(function () {
                        var file = $(this);
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var img = $("<img />");
                                img.attr("style", "width: 100%; height:100%; padding: 10px");
                                img.attr("src", e.target.result);
                                dvPreview.append(img);
                            }
                            reader.readAsDataURL(file[0]);
                    });
                } else {
                    alert("This browser does not support HTML5 FileReader.");
                }
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .wrapper{
            display: flex!important;
            align-items: center!important;
            justify-content: center!important;
        }
        .wrapper .cc {
            position: relative;
            font-family: 'Maven Pro', sans-serif;
            font-size: 16px;
            color: white;
            width: 295px;
            height: 174px;
            border-radius: 16px;
            box-shadow: 0px 0px 18px -1px #2e2e2e;
        }
        .wrapper .cc.visa {
            background-image: linear-gradient(to top right, #52b6fe, #6154fe);
        }
        .wrapper .cc.mastercard {
            background-image: linear-gradient(to top right, #843e3d, #040b2e);
        }
        .wrapper .cc:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 16px;
            background-image: linear-gradient(to top, transparent, #fff);
            opacity: 30%;
        }
        .wrapper .cc svg {
            position: absolute;
            top: 80px;
        }
        .wrapper .cc svg path {
            fill: transparent;
            stroke: #fff;
            stroke-width: 2px;
            stroke-linecap: round;
        }
        .wrapper .cc .container {
            position: absolute;
            top: 25px;
            left: 25px;
            right: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .wrapper .cc .holder {
            position: absolute;
            bottom: 25px;
            left: 25px;
        }
        .wrapper .cc .holder span {
            display: block;
        }
        .wrapper .cc .holder span.name {
            font-size: 14px;
            color: #ccc;
        }
        .wrapper .cc .holder span.number {
            font-weight: 500;
        }

        /* INPUT PREVIEW */
        .preview__all
        {
            display: grid;
            gap: 15px;
        }

        .pre_col
        {
            position: relative;
            border: 1px solid #8e999c;
            border-radius: 12px;
            max-width: 480px;
            padding: 12px;
            display: flex;
            gap: 15px;
        }

        .inp_img_main
        {
            min-width: 200px;
            width: 200px;
            height: 130px;
            max-height: 150px;
            background: #c6ccd0;
            border-radius: 8px;
            overflow: hidden;
        }

        .inp_img_main img
        {
            width: 100%;
            height: 100%;


            /* object-fit: cover;   ---- LOOKUP ----      */

            object-fit: contain;
        }
        .select__file
        {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
        }
        .select__file h4
        {
            margin: 0;
            font-size: 17px;
        }

        .select__file input
        {
            display: none;
        }
        .select__file label
        {
            width: 136px;
            height: 34px;
            text-align: center;
            border: 2px solid #272828;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
        }
        .select__file label:hover
        {
            border: 2px solid #767b7b;
        }
        .select__file label img
        {
            width: 15px;
        }



        /* ====== RESPONE ======= */
        @media screen and (max-width: 600px) {
            .inp_img_main {
                min-width: 180px;
                width: 180px;
                height: 100px;
                max-height: 150px;
                background: #c6ccd0;
                border-radius: 8px;
                overflow: hidden;
            }
        }
        @media screen and (max-width: 450px) {
            .pre_col
            {
                padding: 8px;
            }
            .select__file h4 {
                margin: 0;
                font-size: 15px;
            }
        }

        /* ====== RESPONE ======= */
        @media screen and (max-width: 600px) {
            .inp_img_main {
                min-width: 180px;
                width: 180px;
                height: 100px;
                max-height: 150px;
                background: #c6ccd0;
                border-radius: 8px;
                overflow: hidden;
            }
        }
        @media screen and (max-width: 450px) {
            .pre_col
            {
                padding: 8px;
                gap: 8px;
            }
            .select__file h4 {
                font-size: 14px;
            }
            .select__file label
            {
                width: 130px;
                height: 30px;
            }
        }
        @media screen and (max-width: 380px) {
            .inp_img_main {
                max-width: 120px;
                min-width: 120px;
                width: 100%;
                height: 100px;
                max-height: 150px;
            }
            .select__file
            {
                gap: 6px;
                justify-content: space-around;
            }
        }
    </style>
@endsection
