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

        @if (session()->has('success'))
            <div class="col-md-6 mx-auto alert alert-success mt-3" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-6 mx-auto p-2 mt-1">
                <h3 class="text-center mt-1">@lang('main.how_to_know_limit')</h3>
                <p>
                    {!! __("main.calculator_info_text") !!}    
                </p>
            </div>

            <div class="col-md-6 mx-auto p-2 shadow mt-1" style="border-radius: 24px;">
                <h3 class="text-center mt-1">@lang('main.form_1')</h3>
                <form action="{{ route('my.calculator.store') }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="col-md-12 mt-2">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="lastname" placeholder="@lang('main.form_3')" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="name" placeholder="@lang('main.form_2')" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="fathername" placeholder="@lang('main.form_4')" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <input type="text" class="form-control" id="passport" name="passport" placeholder="@lang('main.form_6')" required>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="row">
                            <div class="col-11">
                                <input type="text" id="pinfl" class="form-control" name="pinfl" placeholder="@lang('main.form_5')" required>
                            </div>
                            <div class="col-1 mt-1">
                                <span class="mt-4" title="{{ __("main.what_is_pinfl") }}" data-toggle="modal" data-target="#exampleModal"><a href="javascript:;"><i class="bi bi-question-circle-fill"></i></a></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <select class="custom-select form-control" name="region" id="inputGroupSelect01" required>
                            <option selected disabled>@lang('main.form_1_1')</option>
                            <option value="Ташкент">@lang('main.form_1_2')</option>
                            <option value="Республика Каракалпакстан">@lang('main.form_1_3')</option>
                            <option value="Андижанская область">@lang('main.form_1_4')</option>
                            <option value="Бухарская область">@lang('main.form_1_5')</option>
                            <option value="Джизакская область">@lang('main.form_1_6')</option>
                            <option value="Кашкадарьинская область">@lang('main.form_1_7')</option>
                            <option value="Навоийская область">@lang('main.form_1_8')</option>
                            <option value="Наманганская область">@lang('main.form_1_9')</option>
                            <option value="Самаркандская область">@lang('main.form_1_10')</option>
                            <option value="Сурхандарьинская область">@lang('main.form_1_11')</option>
                            <option value="Сырдарьинская область">@lang('main.form_1_12')</option>
                            <option value="Ташкентская область">@lang('main.form_1_13')</option>
                            <option value="Ферганская область">@lang('main.form_1_14')</option>
                            <option value="Хорезмская область">@lang('main.form_1_15')</option>
                        </select>
                    </div>
                    <input type="hidden" name="hiddenKundalik" value="hiddenKundalik">
                    <div class="col-md-12 mt-2">
                        <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="@lang('main.form_7')" required>
                    </div>
                    {{-- 
                    <div class="col-md-12 mt-2">
                        <input type="number" class="form-control" name="limit" min="0" placeholder="@lang('main.form_8')">
                    </div>
                    --}}
                    <div class="col-md-12 mt-2">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="bin_code" name="card_number" placeholder="@lang('main.form_18')" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="expiry" name="expiry" placeholder="@lang('main.form_19')" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <textarea class="form-control" name="comment" rows="2" placeholder="@lang('main.form_20')"></textarea>
                    </div>
                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn btn-success w-100" style="border-radius: 20px!important;">@lang('main.form_9')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL FOR IMAGE --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ __("main.what_is_pinfl") }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <b>{{ __("main.if_card") }}</b>
                <img src="{{ asset('images/pinfl/idcard.webp') }}" class="img-fluid" alt="idcard">
                <hr>
                <b>{{ __("main.if_passport") }}</b>
                <img src="{{ asset('images/pinfl/pasport.webp') }}" class="img-fluid" alt="passport">
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('main.to_close') }}</button>
            </div>
        </div>
        </div>
    </div>

</main>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#pinfl').inputmask("9999 9999 9999 99");
            $('#bin_code').inputmask("9999 9999 9999 9999");
            $('#expiry').inputmask("99/99");
            $('#phone_number').inputmask("(99) 999-99-99");
            $('#passport').inputmask('AA9999999');
        });
    </script>
@endsection