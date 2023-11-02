@extends('layouts.app')

@section('seo_title', __('main.checkout'))
@section('meta_description', '')
@section('meta_keywords', '')

@php
$showPricePerMonth = $totalPricePerMonth > 0 && $partnerInstallmentDuration > 0;
@endphp

@section('content')

    @if ($partner_details_name == 'Allgood' || $partner_details_name == 'AnorBank' || $partner_details_name == 'Uzum Nasiya')
        <main class="main">
            <section class="content-header">
                <div class="container">
                    @include('partials.breadcrumbs')
                </div>
            </section>

            <section class="checkout">
                <div class="container">
                    <h1>{{ __('main.checkout') }}</h1>
                    @if (!$cart->isEmpty())
                        <form action="{{ $orderAddUrl }}" method="post" id="checkout-form" class="row checkout-form" enctype="multipart/form-data">
                            <div class="col-lg-8 col-xl-9">
                                @csrf
                                <div class="checkout-form__content radius-14">
                                    {{-- <h4 class="mb-4">{{ __('main.your_order') }}</h4> --}}
                                    <div class="table-responsive">
                                        <table class="table standard-list-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('main.product') }}</th>
                                                    <th>{{ __('main.price') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_product">
                                                @foreach ($cartItems as $cartItem)
                                                    <tr>
                                                        <td>
                                                            <a class="black-link" href="{{ $cartItem->associatedModel->url }}" target="_blank">{{ $cartItem->name }}</a>
                                                            <strong> × {{ $cartItem->quantity }}</strong>
                                                            @if ($cartItem->quantity > $cartItem->availableQuantity)
                                                                <br>
                                                                <strong class="text-danger">{{ __('main.available') }}: {{ $cartItem->availableQuantity }}</strong>
                                                            @endif
                                                        </td>
                                                        @if (!isset(auth()->user()->id))
                                                        <td class="text-nowrap">
                                                            @if ($showPricePerMonth)
                                                            {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                            @else
                                                            {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]  ) }}
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td class="text-nowrap">
                                                            @if ($showPricePerMonth)
                                                                @if (!empty(auth()->user()->coupon_sum))
                                                                    @if (auth()->user()->is_coupon_used == 'no')
                                                                        @php
                                                                            $toMonthCalculate = auth()->user()->coupon_sum / $partnerInstallmentDuration;
                                                                            $totalToMonth = $cartItemsPrices[$cartItem->id] - $toMonthCalculate;
                                                                        @endphp
                                                                    <p class="m-0 p-0">{{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }}</p>
                                                                    {{-- <img src="{{ asset('img/coupon2.svg') }}" style="color: yellowgreen;" width="20px;" height="20px;"> {{ Helper::formatPrice($totalToMonth) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span> --}}
                                                                    @else
                                                                    {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                    @endif
                                                                @else
                                                                {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                @endif
                                                            @else
                                                                @if (!empty(auth()->user()->coupon_sum))
                                                                    @if (auth()->user()->is_coupon_used == 'no')
                                                                        @php
                                                                            $total = $cartItemsPrices[$cartItem->id] - auth()->user()->coupon_sum;
                                                                        @endphp
                                                                    <p class="m-0 p-0"><del>{{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }}</del></p>
                                                                    <img src="{{ asset('img/coupon2.svg') }}" style="color: yellowgreen;" width="20px;" height="20px;"> {{ Helper::formatPrice($total) }}
                                                                    @else
                                                                    {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]  ) }}
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        </td>
                                                    @endif
                                                    </tr>

                                                    <input type="hidden" id="yandex-ecom-product-name" value="{{ $cartItem->name }}">
                                                    <input type="hidden" id="yandex-ecom-product-quantity" value="{{ $cartItem->quantity }}">
                                                    <input type="hidden" id="yandex-ecom-product-price" value="{{ $cartItem->price }}">
                                                    <input type="hidden" id="yandex-ecom-product-id" value="{{ $cartItem->id }}">
                                                @endforeach

                                                @if (!empty(auth()->user()->coupon_sum))
                                                    @if (auth()->user()->is_coupon_used == 'no')
                                                        <tr>
                                                            <td>
                                                                {{ __('main.coupon') }}
                                                            </td>
                                                            <td>
                                                                {{ Helper::formatPrice(auth()->user()->coupon_sum) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif

                                            </tbody>
                                            <tfoot id="tfoot">
                                                @if ($shippingPrice > 0)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ __('main.delivery') }}</strong>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <strong>{{ Helper::formatPrice($shippingPrice) }}</strong>
                                                        </td>
                                                    </tr>
                                                @endif
                                                {{-- @if ($cartItems->count() > 1) --}}
                                                    <tr>
                                                        <td>
                                                            <h4 class="m-0">{{ __('main.total') }}</h4>
                                                        </td>
                                                        @if (isset(auth()->user()->id))
                                                        @if (!empty(auth()->user()->coupon_sum))
                                                            @if (auth()->user()->is_coupon_used == 'no')
                                                                <td class="text-nowrap">
                                                                    <p class="m-0"> <del>
                                                                        @if ($showPricePerMonth)
                                                                        {{ Helper::formatPrice($totalPricePerMonth) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                        @else
                                                                        {{ Helper::formatPrice($totalPrice) }}
                                                                        @endif
                                                                    </del>
                                                                    </p>

                                                                    @php
                                                                        $total = $totalPrice - auth()->user()->coupon_sum;
                                                                    @endphp
                                                                    <h4 class="m-0"> <img src="{{ asset('img/coupon2.svg') }}" style="color: yellowgreen;" width="20px;" height="20px;"> {{ Helper::formatPrice($total) }}</h4>
                                                                </td>
                                                            @endif
                                                        @else
                                                            <td class="text-nowrap">
                                                                <h4 class="m-0">
                                                                    @if ($showPricePerMonth)
                                                                        {{ Helper::formatPrice($totalPricePerMonth) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                    @else
                                                                        {{ Helper::formatPrice($totalPrice) }}
                                                                    @endif
                                                                </h4>
                                                            </td>
                                                        @endif
                                                    @else
                                                        <td class="text-nowrap">
                                                            <h4 class="m-0">
                                                                @if ($showPricePerMonth)
                                                                {{ Helper::formatPrice($totalPricePerMonth) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                @else
                                                                {{ Helper::formatPrice($totalPrice) }}
                                                                @endif
                                                            </h4>
                                                        </td>
                                                    @endif
                                                    </tr>
                                                {{-- @endif --}}

                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                @if ($showPricePerMonth)
                                    @auth
                                        @if (!empty(auth()->user()->coupon_sum))
                                            @if (auth()->user()->is_coupon_used == 'no')
                                                @php
                                                    $totalWithCoupon = $totalPrice - auth()->user()->coupon_sum;
                                                @endphp
                                                <div class="alert alert-success border" role="alert">
                                                    @if (app()->getlocale() == 'ru')
                                                        <span> <b> Напоминание! </b> <br>
                                                            Вы получили скидку  <b> {{ Helper::formatPrice(auth()->user()->coupon_sum) }} </b>
                                                            по купону. Итоговый счет со скидкой составил <b> {{ Helper::formatPrice($totalToMonth) }} </b> в месяц.
                                                            Скидка действует один раз, в следующий раз не получится воспользоваться ею.
                                                        </span>
                                                    @else
                                                        <span> <b> Eslatma! </b> <br>
                                                            Siz kupon orqali <b> {{ Helper::formatPrice(auth()->user()->coupon_sum) }} </b> chegirmaga ega edingiz. Chegirma bilan yakuniy hisob oyiga <b> {{ Helper::formatPrice($totalToMonth) }} </b> ni tashkil qildi.
                                                            <br>
                                                            Chegirma bir marta uchun xizmat qiladi, keyingi safar undan foydalana olmaysiz.
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    @endauth
                                @else
                                    @auth
                                        @if (!empty(auth()->user()->coupon_sum))
                                            @if (auth()->user()->is_coupon_used == 'no')
                                                @php
                                                    $totalWithCoupon = $totalPrice - auth()->user()->coupon_sum;
                                                @endphp
                                                <div class="alert alert-success border" role="alert">
                                                    @if (app()->getlocale() == 'ru')
                                                        <span> <b> Напоминание! </b> <br>
                                                            Вам была предоставлена скидка по купону <b>({{ Helper::formatPrice(auth()->user()->coupon_sum) }})</b>. Окончательный счет составил: <b> {{ Helper::formatPrice($totalWithCoupon) }} </b>.
                                                            <br>
                                                            Скидка действует один раз, в следующий раз ею воспользоваться нельзя.
                                                        </span>
                                                    @else
                                                        <span> <b> Eslatma! </b> <br>
                                                            Siz kupon orqali <b> {{ Helper::formatPrice(auth()->user()->coupon_sum) }} </b> chegirmaga ega edingiz. Chegirma bilan yakuniy hisob <b> {{ Helper::formatPrice($totalWithCoupon) }} </b> ni tashkil qildi.
                                                            <br>
                                                            Chegirma bir marta uchun xizmat qiladi, keyingi safar undan foydalana olmaysiz.
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    @endauth
                                @endif

                                <div class="checkout-form__content radius-14">
                                    <h4 class="mb-4">{{ __('main.contact_information') }}</h4>
                                    <div class="mb-4">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('main.form.your_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" id="storage_name" class="form-control  @error('name') is-invalid @enderror"
                                                value="{{ old('name', optional(auth()->user())->name) }}" required>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">
                                                <span>{{ __('main.form.phone_number') }}</span>
                                                <span class="text-danger">*</span>
                                                {{-- <small class="text-muted">({{ __('main.phone_number_example') }})</small> --}}
                                            </label>

                                            <input type="tel" name="phone_number" id="storage_phone"
                                                class="phone-input-mask form-control  @error('phone_number') is-invalid @enderror"
                                                value="{{ old('phone_number', optional(auth()->user())->phone_number) ?? '' }}"
                                                required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                                            @error('phone_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        {{--
                                        <div class="form-group">
                                            <input type="hidden" name="latitude" value="">
                                            <input type="hidden" name="longitude" value="">
                                            <input type="hidden" name="location_accuracy" value="">
                                            <label class="control-label">{{ __('main.location') }}</label>
                                            <div id="location-text"></div>

                                            <div class="iframe-div">

                                            </div>

                                             
                                            <div>
                                                <button type="button" class="btn btn-primary get-location-btn">{{ __('main.determine_geolocation') }}</button>
                                            </div>
                                        </div>
                                        --}}

                                        <input type="hidden" name="address_line_1" value="">
                                        {{-- <input type="hidden" name="latitude" value="123">
                                        <input type="hidden" name="longitude" value="123">
                                        <input type="hidden" name="location_accuracy" value="123"> --}} 
                                        <input type="hidden" name="location_accuracy" value="123">
                                        <input type="hidden" name="communication_method" value="2">
                                        <input type="hidden" name="message" value="">

                                        <input type="hidden" name="installment_payment_months" value="{{ $partnerInstallmentDuration }}">
                                        <input type="hidden" name="total_price_per_month" value="{{ $totalPricePerMonth }}">
                                        @if($partner_details_name == 'Allgood')
                                            <input type="hidden" name="payment_method_id" value="13">
                                        @elseif($partner_details_name == 'AnorBank')
                                            <input type="hidden" name="payment_method_id" value="15">
                                        @elseif($partner_details_name == 'Uzum Nasiya')
                                            <input type="hidden" name="payment_method_id" value="16">
                                        @endif

                                        {{-- <div class="form-group">
                                            <label class="control-label">
                                                <span>{{ __('main.form.email') }} <span class="text-danger">*</span></span>
                                            </label>

                                            <input type="email" name="email"
                                                class="form-control  @error('email') is-invalid @enderror"
                                                value="{{ old('email', optional(auth()->user())->email) ?? '' }}" required>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div> --}}


                                        {{-- <div class="form-group d-none">
                                            <label class="control-label">{{ __('main.order_type') }} <span
                                                    class="text-danger">*</span></label></label>
                                            @php
                                            $checkedOrderTypeKey = old('type') ?: 0;
                                            @endphp
                                            @foreach ($orderTypes as $orderTypeKey => $orderType)
                                            <div class="form-check">
                                                <input class="form-check-input" id="order_type_{{ $orderTypeKey }}" type="radio"
                                                    name="type" value="{{ $orderTypeKey }}" @if ($checkedOrderTypeKey == $orderTypeKey)
                                                    checked @endif>
                                                <label class="form-check-label" for="order_type_{{ $orderTypeKey }}">
                                                    {{ $orderType }}
                                                </label>
                                            </div>
                                            @endforeach
                                            @error('type')
                                            <div class="invalid-feedback d-block" role="alert">
                                                <strong>{{ __('main.choose_value') }}</strong>
                                            </div>
                                            @enderror
                                        </div> --}}
                                        {{-- <div class="form-group d-none">

                                            <label for="create_an_account_checkbox" data-toggle="collapse"
                                                data-target="#create_an_account_block" aria-controls="create_an_account_block">
                                                <input id="create_an_account_checkbox" type="checkbox" name="create_an_account" />
                                                Create an account?
                                            </label>

                                            <div id="create_an_account_block" class="collapse one">
                                                <div class="card-body1">
                                                    <label> Account password <span>*</span></label>
                                                    <input name="password" type="password" class="form-control">
                                                </div>
                                            </div>
                                        </div> --}}
                                        {{-- <div class="form-group">
                                            <input id="public_offer" name="public_offer" type="checkbox" required>
                                            <label for="public_offer">
                                                {!! __('main.accept_the_terms', ['url' => '<a href="' . $publicOfferPage->url . '"
                                                    target="_blank" class="text-primary">' . __('main.of_public_offer') . '</a>'])
                                                !!}
                                                {{ __('main.i_confirm_order') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            @error('public_offer')
                                            <div class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div> --}}
                                    </div>
                                </div>

                                {{-- PASSPORT INFORMATION 
                                <div class="checkout-form__content radius-14 @if(!empty(auth()->user()) && !empty(auth()->user()->passport_main_image) && !empty(auth()->user()->passport_address_image) && !empty(auth()->user()->passport_additional_image) && !empty(auth()->user()->plastic_card_image)) d-none @endif">
                                    <h4 class="mb-4">{{ __('main.OptionalRequirements') }}</h4>
                                    @if (empty(auth()->user()))
                                        <div class="mb-4">
                                            <div class="row mx-auto">
                                                <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                            {{ __("main.passportSelfie") }}
                                                        </h4>
                                                        <div class="for_upload" onchange="preview()">
                                                            <label for="ImageMediasSelfie">
                                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                            </label>
                                                            <input type="file" name="passport_main_string" id="ImageMediasSelfie"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                            {{ __("main.passportFace") }}
                                                        </h4>
                                                        <div class="for_upload" onchange="preview()">
                                                            <label for="ImageMediasAddress">
                                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                            </label>
                                                            <input type="file" name="passport_address_string" id="ImageMediasAddress"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                            {{ __("main.passportBackFace") }}
                                                        </h4>
                                                        <div class="for_upload" onchange="preview()">
                                                            <label for="ImageMediasAdditional">
                                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                            </label>
                                                            <input type="file" name="passport_additional_string" id="ImageMediasAdditional"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-4">
                                            <div class="row">
                                                @if (empty(auth()->user()->passport_main_image))
                                                    <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                                {{ __("main.passportSelfie") }}
                                                            </h4>
                                                            <div class="for_upload" onchange="preview()">
                                                                <label for="ImageMediasSelfie">
                                                                    <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                    <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                                </label>
                                                                <input type="file" name="passport_main_string" id="ImageMediasSelfie"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (empty(auth()->user()->passport_address_image))
                                                    <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                                {{ __("main.passportFace") }}
                                                            </h4>
                                                            <div class="for_upload" onchange="preview()">
                                                                <label for="ImageMediasAddress">
                                                                    <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                    <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                                </label>
                                                                <input type="file" name="passport_address_string" id="ImageMediasAddress"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (empty(auth()->user()->passport_additional_image))
                                                    <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                                {{ __("main.passportBackFace") }}
                                                            </h4>
                                                            <div class="for_upload" onchange="preview()">
                                                                <label for="ImageMediasAdditional">
                                                                    <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                    <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                                </label>
                                                                <input type="file" name="passport_additional_string" id="ImageMediasAdditional"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                PASSPORT INFORMATION ends --}}

                                @if ($partner_details_name == "Uzum Nasiya")
                                    <div class="checkout-form__content radius-14 uzumform bg-light p-2">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div>
                                                    <img src="{{ asset('images/partners/uzum.png') }}" alt="Uzum" class="img-fluid" width="150">
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <p class="mt-3"> <b>Вход в Uzum Nasiya</b> <br>
                                                    Пожалуйста, авторизуйтесь, чтобы войти в аккаунт Uzum Nasiya</p>
                                            </div>
                                        </div>
                                        <hr>
                                        @if (!empty(session()->get('successUzumData')))
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select name="uzumMonths" id="" class="w-100 form-control">
                                                        @foreach ($successUzumData as $item)
                                                            <option value="">{{ $item->tariff }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <br>
                                                    <button type="submit" class="theme-btn place-order-btn radius-6 @if (!$checkoutAvailable) disabled @endif @if($partner_details_name != 'Uzum Nasiya') disabled @endif"
                                                        @if (!$checkoutAvailable) disabled @endif  @if($partner_details_name != 'Uzum Nasiya') disabled @endif>{{ __('main.place_order') }}</button>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>{{ __('main.form.phone_number') }}</label>
                                                <input type="text" name="uzum_phone_number"
                                                    class="form-control phone-input-mask"
                                                    pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                                                    <input type="hidden" name="current_url" id="current_url_input">
                                            </div>
                                            <div class="col-md-6">
                                                <br>
                                                <button type="submit" class="theme-btn radius-6 @if (!$checkoutAvailable) disabled @endif @if($partner_details_name != 'Uzum Nasiya') disabled @endif"
                                                    @if (!$checkoutAvailable) disabled @endif  @if($partner_details_name != 'Uzum Nasiya') disabled @endif>{{ __('main.place_order') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="checkout-form__content radius-14">

                                        @if($partner_details_name == 'AnorBank')
                                        <div class="row">
                                            <div class="col-12">
                                                <img src="https://www.anorbank.uz/images/logo.svg" alt=""> <br> 
                                                Оплатить по карте рассрочки Анор банк
                                            </div>
                                        </div>

                                        

                                        <hr>
                                        @endif

                                        <div class="row form-group">
                                            <div class="col-8">
                                                <h4 class="mb-3">{{ __('main.your_card') }}</h4>
                                                <input type="text" class="form-control card-input-mask @if($partner_details_name == 'AnorBank') card_anor @endif" @if($partner_details_name != 'Allgood') required @endif name="card_number" value="@if(session()->has('successCard')) {{ session()->get('successCard') }} @endif">
                                                <span id="card_validation_text"></span>
                                                <span id="card_validation_text2"></span>
                                            </div>
                                            <div class="col-4">
                                                <h4 class="mb-3">{{ __('main.your_card_val') }}</h4>
                                                <input type="text" class="form-control card-available-input-mask" name="card_validation_date" @if($partner_details_name != 'Allgood') required @endif value="@if(session()->has('successCardExpiry')) {{ session()->get('successCardExpiry') }} @endif">
                                            </div>
                                        </div> 
                                        @if (!empty(session()->has("askOtp")))
                                        <div class="row">
                                            @if (!empty(session()->has("askOtpSuccess")))
                                            <div class="col-6">
                                                <h4 class="mb-3" class="mb-2">Код</h4>
                                                <input type="text" class="form-control" placeholder="xxxxxx" name="otp">
                                            </div>
                                            @endif
                                            <div class="col-6">
                                                <h4 class="mb-3">Cообщение</h4>
                                                <div class="alert alert-primary p-1" id="card_validation_ask_otp" role="alert">
                                                    {{ session()->get("askOtp") }}
                                                </div>               
                                                {{--                                        
                                                    <span style="color: #A80F4C;" id="card_validation_ask_otp" class="mt-3">
                                                        {{ session()->get("askOtp") }}
                                                    </span>
                                                --}}
                                            </div>
                                        </div>
                                        @endif
                                        <br>
                                        @if (!$checkoutAvailable)
                                            <div class="text-danger my-2">{{ __('main.checkout_not_available') }} <a href="{{ route('cart.index') }}">{{ __('main.go_to_cart') }}</a></div>
                                        @endif

                                        @php
                                            $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
                                            $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
                                            $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
                                            $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
                                            $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
                                        @endphp 

                                        <div>
                                                @if($partner_details_name == 'AnorBank')
                                                    <button type="submit"  style="background-color: #0648BE; color: white;" class="btn btn-sm place-order-btn card_validation_button radius-6 @if (!$checkoutAvailable) disabled @endif @if($partner_details_name != 'Allgood') disabled @endif"
                                                @if (!$checkoutAvailable) disabled @endif  @if($partner_details_name != 'Allgood') disabled @endif>Оплатить Anor Bank</button>
                                                    <a @if ($Android) href="https://play.google.com/store/apps/details?id=uz.anormobile.retail" @else href="https://apps.apple.com/app/anorbank/id1579623268" @endif
                                                    class="btn btn-sm place-order-btn radius-6" style="background-color: #A80F4C; color: white;">Регистрация Anor Bank</a>
                                                @else
                                                    <button type="submit" class="theme-btn place-order-btn card_validation_button radius-6 @if (!$checkoutAvailable) disabled @endif @if($partner_details_name != 'Allgood') disabled @endif"
                                                    @if (!$checkoutAvailable) disabled @endif  @if($partner_details_name != 'Allgood') disabled @endif>{{ __('main.place_order') }}</button>
                                                @endif
                                        </div>
                                        @if($partner_details_name == 'AnorBank')
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <span> <i class="bi bi-info-circle-fill" style="color: #A80F4C;"></i> Нет личной страницы в AnorBank? Воспользуйтесь <a href="https://www.anorbank.uz/cards/card-installment/" style="color: #A80F4C;"> <b> инструкцией. </b> </a></span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                @endif

                            </div>
                            <div class="col-lg-4 col-xl-3"></div>
                        </form>
                    @else
                        <div class="my-5 lead text-center">{{ __('main.cart_is_empty') }}</div>
                    @endif
                </div>
            </section>
        </main>
    @else
        <main class="main">
            <section class="content-header">
                <div class="container">
                    @include('partials.breadcrumbs')
                </div>
            </section>

            <section class="checkout">
                <div class="container">
                    <h1>{{ __('main.checkout') }}</h1>
                    @if (!$cart->isEmpty())
                        <form action="{{ $orderAddUrl }}" method="post" id="checkout-form" class="row checkout-form" enctype="multipart/form-data">
                            <div class="col-lg-8 col-xl-9">
                                @csrf
                                <div class="checkout-form__content radius-14">
                                    {{--<h4 class="mb-4">{{ __('main.your_order') }}</h4>--}}
                                    <div class="table-responsive">
                                        <table class="table standard-list-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('main.product') }}</th>
                                                    <th>{{ __('main.price') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_product">
                                                @foreach ($cartItems as $cartItem)
                                                    <tr>
                                                        <td>
                                                            <a class="black-link" href="{{ $cartItem->associatedModel->url }}" target="_blank">{{ $cartItem->name }}</a>
                                                            <strong> × {{ $cartItem->quantity }}</strong>
                                                            @if ($cartItem->quantity > $cartItem->availableQuantity)
                                                                <br>
                                                                <strong class="text-danger">{{ __('main.available') }}: {{ $cartItem->availableQuantity }}</strong>
                                                            @endif
                                                        </td>
                                                        @if (!isset(auth()->user()->id))
                                                        <td class="text-nowrap">
                                                            @if ($showPricePerMonth)
                                                            {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                            @else
                                                            {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]  ) }}
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td class="text-nowrap">
                                                            @if ($showPricePerMonth)
                                                                @if (!empty(auth()->user()->coupon_sum))
                                                                    @if (auth()->user()->is_coupon_used == 'no')
                                                                        @php
                                                                            $toMonthCalculate = auth()->user()->coupon_sum / $partnerInstallmentDuration;
                                                                            $totalToMonth = $cartItemsPrices[$cartItem->id] - $toMonthCalculate;
                                                                        @endphp
                                                                    <p class="m-0"><del>{{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }}</del></p>
                                                                    {{-- <img src="{{ asset('img/coupon2.svg') }}" style="color: yellowgreen;" width="20px;" height="20px;"> {{ Helper::formatPrice($totalToMonth) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span> --}}
                                                                    @else
                                                                    {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                    @endif
                                                                @else
                                                                    {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                @endif
                                                            @else
                                                                @if (!empty(auth()->user()->coupon_sum))
                                                                    @if (auth()->user()->is_coupon_used == 'no')
                                                                        @php
                                                                            $totalOT = $cartItemsPrices[$cartItem->id] - auth()->user()->coupon_sum;
                                                                        @endphp
                                                                    <p class="m-0 p-0">{{ Helper::formatPrice($cartItemsPrices[$cartItem->id]) }}</p>
                                                                    {{-- <img src="{{ asset('img/coupon2.svg') }}" style="color: yellowgreen;" width="20px;" height="20px;"> {{ Helper::formatPrice($totalOT) }} --}}
                                                                    @else
                                                                    {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]  ) }}
                                                                    @endif
                                                                @else
                                                                    {{ Helper::formatPrice($cartItemsPrices[$cartItem->id]  ) }}
                                                                @endif

                                                            @endif
                                                        </td>
                                                    @endif
                                                    </tr>

                                                    <input type="hidden" id="yandex-ecom-product-name" value="{{ $cartItem->name }}">
                                                    <input type="hidden" id="yandex-ecom-product-quantity" value="{{ $cartItem->quantity }}">
                                                    <input type="hidden" id="yandex-ecom-product-price" value="{{ $cartItem->price }}">
                                                    <input type="hidden" id="yandex-ecom-product-id" value="{{ $cartItem->id }}">
                                                @endforeach
                                                @if (!empty(auth()->user()->coupon_sum))
                                                    @if (auth()->user()->is_coupon_used == 'no')
                                                        <tr>
                                                            <td>
                                                                {{ __('main.coupon') }}
                                                            </td>
                                                            <td>
                                                                {{ Helper::formatPrice(auth()->user()->coupon_sum) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            </tbody>
                                            <tfoot id="tfoot">
                                                @if ($shippingPrice > 0)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ __('main.delivery') }}</strong>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <strong>{{ Helper::formatPrice($shippingPrice) }}</strong>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if ($cartItems->count() > 1)
                                                    <tr>
                                                        <td>
                                                            <h4 class="m-0">{{ __('main.total') }}</h4>
                                                        </td>
                                                        @if (isset(auth()->user()->id))
                                                        @if (!empty(auth()->user()->coupon_sum))
                                                            @if (auth()->user()->is_coupon_used == 'no')
                                                                <td class="text-nowrap">
                                                                    <p class="m-0"> <del>
                                                                        @if ($showPricePerMonth)
                                                                        {{ Helper::formatPrice($totalPricePerMonth) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                        @else
                                                                        {{ Helper::formatPrice($totalPrice) }}
                                                                        @endif
                                                                    </del>
                                                                    </p>

                                                                    @php
                                                                        $total = $totalPrice - auth()->user()->coupon_sum;
                                                                    @endphp
                                                                    <h4 class="m-0"> <img src="{{ asset('img/coupon2.svg') }}" style="color: yellowgreen;" width="20px;" height="20px;"> {{ Helper::formatPrice($total) }}</h4>
                                                                </td>
                                                            @endif
                                                        @else
                                                            <td class="text-nowrap">
                                                                <h4 class="m-0">
                                                                    @if ($showPricePerMonth)
                                                                        {{ Helper::formatPrice($totalPricePerMonth) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                    @else
                                                                        {{ Helper::formatPrice($totalPrice) }}
                                                                    @endif
                                                                </h4>
                                                            </td>
                                                        @endif
                                                    @else
                                                        <td class="text-nowrap">
                                                            <h4 class="m-0">
                                                                @if ($showPricePerMonth)
                                                                {{ Helper::formatPrice($totalPricePerMonth) }} / {{ $partnerInstallmentDuration }} <span class="text-lowercase">{{ __('main.month2') }}</span>
                                                                @else
                                                                {{ Helper::formatPrice($totalPrice) }}
                                                                @endif
                                                            </h4>
                                                        </td>
                                                    @endif
                                                    </tr>
                                                @endif

                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                @if ($showPricePerMonth)
                                    @auth
                                        @if (!empty(auth()->user()->coupon_sum))
                                            @if (auth()->user()->is_coupon_used == 'no')
                                                @php
                                                    $totalWithCoupon = $totalPrice - auth()->user()->coupon_sum;
                                                @endphp
                                                <div class="alert alert-success border" role="alert">
                                                    @if (app()->getlocale() == 'ru')
                                                        <span> <b> Напоминание! </b> <br>
                                                            Вы получили скидку  <b> {{ Helper::formatPrice(auth()->user()->coupon_sum) }} </b>
                                                            по купону. Итоговый счет со скидкой составил <b> {{ Helper::formatPrice($totalToMonth) }} </b> в месяц.
                                                            Скидка действует один раз, в следующий раз не получится воспользоваться ею.
                                                        </span>
                                                    @else
                                                        <span> <b> Eslatma! </b> <br>
                                                            Siz kupon orqali <b> {{ Helper::formatPrice(auth()->user()->coupon_sum) }} </b> chegirmaga ega edingiz. Chegirma bilan yakuniy hisob oyiga <b> {{ Helper::formatPrice($totalToMonth) }} </b> ni tashkil qildi.
                                                            <br>
                                                            Chegirma bir marta uchun xizmat qiladi, keyingi safar undan foydalana olmaysiz.
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    @endauth
                                @else
                                    @auth
                                        @if (!empty(auth()->user()->coupon_sum))
                                            @if (auth()->user()->is_coupon_used == 'no')
                                                @php
                                                    $totalWithCoupon = $totalPrice - auth()->user()->coupon_sum;
                                                @endphp
                                                <div class="alert alert-success border" role="alert">
                                                    @if (app()->getlocale() == 'ru')
                                                        <span> <b> Напоминание! </b> <br>
                                                            Вам была предоставлена скидка по купону <b>({{ Helper::formatPrice(auth()->user()->coupon_sum) }})</b>. Окончательный счет составил: <b> {{ Helper::formatPrice($totalWithCoupon) }} </b>.
                                                            <br>
                                                            Скидка действует один раз, в следующий раз ею воспользоваться нельзя.
                                                        </span>
                                                    @else
                                                        <span> <b> Eslatma! </b> <br>
                                                            Siz kupon orqali <b> {{ Helper::formatPrice(auth()->user()->coupon_sum) }} </b> chegirmaga ega edingiz. Chegirma bilan yakuniy hisob <b> {{ Helper::formatPrice($totalWithCoupon) }} </b> ni tashkil qildi.
                                                            <br>
                                                            Chegirma bir marta uchun xizmat qiladi, keyingi safar undan foydalana olmaysiz.
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    @endauth
                                @endif

                                {{-- OPROS FOR INTEND ALIF ABOUT LABOR CONDITION
                                <div class="checkout-form__content radius-14" id="hideHasWorkPermitBlock">
                                    <h4 class="mb-4">{{ __('main.opros') }} <span class="text-danger">*</span></h4>
                                    <div class="mb-4">
                                        <a href="javascript:;">{{ __('main.opros1') }}</a>
                                        <div class="form-group bnpl_question_1">
                                            <input type="radio" class="mt-2" name="has_work_permit" id="text_1" value="yes"> <label for="text_1">{{ __('main.opros2') }}</label> <br>
                                            <input type="radio" name="has_work_permit" id="text_2" value="no"> <label for="text_2">{{ __('main.opros3') }}</label>
                                        </div>
                                        <hr>
                                        <a href="javascript:;">{{ __('main.opros4') }}</a>
                                        <div class="form-group bnpl_question_2">
                                            <input type="radio" class="mt-2" name="has_bnpl_already" id="text_3" value="yes"> <label for="text_3">{{ __('main.opros5') }}</label> <br>
                                            <input type="radio" name="has_bnpl_already" id="text_4" value="no"> <label for="text_4">{{ __('main.opros6') }}</label>
                                        </div>
                                    </div>
                                </div>
                                --}}

                                <div class="checkout-form__content radius-14">
                                    <h4 class="mb-4">{{ __('main.contact_information') }}</h4>
                                    <div class="mb-4">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('main.form.your_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" id="storage_name" class="form-control  @error('name') is-invalid @enderror"
                                                value="{{ old('name', optional(auth()->user())->name) }}" required>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">
                                                <span>{{ __('main.form.phone_number') }}</span>
                                                <span class="text-danger">*</span>
                                                {{-- <small class="text-muted">({{ __('main.phone_number_example') }})</small> --}}
                                            </label>

                                            <input type="tel" name="phone_number" id="storage_phone"
                                                class="phone-input-mask form-control  @error('phone_number') is-invalid @enderror"
                                                value="{{ old('phone_number', optional(auth()->user())->phone_number) ?? '' }}"
                                                required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                                            @error('phone_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label">{{ __('main.form.message') }}</label>
                                            <input type="text" name="message"
                                                class="form-control  @error('message') is-invalid @enderror"
                                                value="{{ old('message') }}">
                                            @error('message')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        
                                        {{-- <div class="form-group">
                                            <label class="control-label">
                                                <span>{{ __('main.form.email') }} <span class="text-danger">*</span></span>
                                            </label>

                                            <input type="email" name="email"
                                                class="form-control  @error('email') is-invalid @enderror"
                                                value="{{ old('email', optional(auth()->user())->email) ?? '' }}" required>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div> 
                                        <div class="form-group">
                                            <label class="control-label">{{ __('main.address') }}</label>
                                            <input type="text" name="address_line_1"
                                                class="form-control  @error('address_line_1') is-invalid @enderror"
                                                value="{{ old('address_line_1', optional($address)->address_line_1) ?? '' }}">
                                            @error('address_line_1')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div> --}}

                                        {{--
                                        <div class="form-group">
                                            <input type="hidden" name="latitude" value="">
                                            <input type="hidden" name="longitude" value="">
                                            <input type="hidden" name="location_accuracy" value="">
                                            <label class="control-label">{{ __('main.location') }}</label>
                                            <div id="location-text"></div>

                                            <div class="iframe-div">

                                            </div>

                                             
                                            <div>
                                                <button type="button" class="btn btn-primary get-location-btn">{{ __('main.determine_geolocation') }}</button>
                                            </div>
                                        </div>
                                        --}}

                                        <div class="form-group d-none">
                                            <label class="control-label d-block">{{ __('main.communication_method') }}</label>
                                            @php
                                                $checkedCommunicationMethodKey = old('communication_method') ?: 0;
                                            @endphp
                                            @foreach ($communicationMethods as $communicationMethodKey => $communicationMethod)
                                                <div class="form-check d-inline-block mr-2">
                                                    <input class="form-check-input"
                                                        id="communication_method_{{ $communicationMethodKey }}" type="radio"
                                                        name="communication_method" value="{{ $communicationMethodKey }}"
                                                        @if ($checkedCommunicationMethodKey == $communicationMethodKey) checked @endif
                                                        required>
                                                    <label class="form-check-label"
                                                        for="communication_method_{{ $communicationMethodKey }}">
                                                        {{ $communicationMethod }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            @error('communication_method')
                                                <div class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ __('main.choose_value') }}</strong>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- PASSPORT INFORMATION 
                                @if ($partner_details_name != 'Alifshop' || $partner_details_name != 'Intend')
                                <div class="checkout-form__content passport_second_form radius-14 @if(!empty(auth()->user()) && !empty(auth()->user()->passport_main_image) && !empty(auth()->user()->passport_address_image) && !empty(auth()->user()->passport_additional_image) && !empty(auth()->user()->plastic_card_image)) d-none @endif">
                                    <h4 class="mb-4">{{ __('main.OptionalRequirements') }}</h4>
                                    @if (empty(auth()->user()))
                                        <div class="mb-4">
                                            <div class="row mx-auto">
                                                <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                            {{ __("main.passportSelfie") }}
                                                        </h4>
                                                        <div class="for_upload" onchange="preview()">
                                                            <label for="ImageMediasSelfie">
                                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                            </label>
                                                            <input type="file" name="passport_main_string" id="ImageMediasSelfie"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                            {{ __("main.passportFace") }}
                                                        </h4>
                                                        <div class="for_upload" onchange="preview()">
                                                            <label for="ImageMediasAddress">
                                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                            </label>
                                                            <input type="file" name="passport_address_string" id="ImageMediasAddress"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                            {{ __("main.passportBackFace") }}
                                                        </h4>
                                                        <div class="for_upload" onchange="preview()">
                                                            <label for="ImageMediasAdditional">
                                                                <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                            </label>
                                                            <input type="file" name="passport_additional_string" id="ImageMediasAdditional"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-4">
                                            <div class="row">
                                                @if (empty(auth()->user()->passport_main_image))
                                                    <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                                {{ __("main.passportSelfie") }}
                                                            </h4>
                                                            <div class="for_upload" onchange="preview()">
                                                                <label for="ImageMediasSelfie">
                                                                    <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                    <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                                </label>
                                                                <input type="file" name="passport_main_string" id="ImageMediasSelfie"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (empty(auth()->user()->passport_address_image))
                                                    <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                                {{ __("main.passportFace") }}
                                                            </h4>
                                                            <div class="for_upload" onchange="preview()">
                                                                <label for="ImageMediasAddress">
                                                                    <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                    <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                                </label>
                                                                <input type="file" name="passport_address_string" id="ImageMediasAddress"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (empty(auth()->user()->passport_additional_image))
                                                    <div class="pre_col col-md-6 mx-auto shadow border-0 mb-4">
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
                                                                {{ __("main.passportBackFace") }}
                                                            </h4>
                                                            <div class="for_upload" onchange="preview()">
                                                                <label for="ImageMediasAdditional">
                                                                    <img src="{{ asset('img/upload.png') }}" alt="upload__img">
                                                                    <p class="m-0">{{ __("main.zagruzit") }}</p>
                                                                </label>
                                                                <input type="file" name="passport_additional_string" id="ImageMediasAdditional"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @endif
                                PASSPORT INFORMATION ends --}}

                                <div class="checkout-form__content radius-14">

                                    <h3>{{ __('main.payment_method') }} <span class="text-danger">*</span></h3>
                                    <div class="form-group">
                                        @php
                                            $checkedPaymentMethodId = old('payment_method_id'); // default = 1 - cash
                                            if (!$checkedPaymentMethodId) {
                                                $checkedPaymentMethodId = $paymentMethods->first()->id;
                                            }
                                        @endphp
                                        {{-- <label class="radio-label">
                                            <input type="radio" class="payment-method-input" name="payment_method_id" data-toggle="modal" data-target="#exampleModalCenter">
                                            <strong>{{ __('main.installment_payment_buy') }}</strong>
                                        </label> --}}

                                        @foreach ($paymentMethods as $paymentMethod)
                                            @if ($paymentMethod->code == 'cash' || $paymentMethod->code == 'allgood')
                                            <label class="radio-label">
                                                <input type="radio" class="payment-method-input" name="payment_method_id" data-toggle="modal" data-target="#exampleModalCenter">
                                                <strong>{{ __('main.installment_payment_buy') }}</strong>
                                            </label>
                                            @endif
                                        @endforeach

                                        @foreach ($paymentMethods as $paymentMethod)
                                            <label class="radio-label">
                                                <input id="payment_method_{{ $paymentMethod->id }}" class="payment-method-input" type="radio" name="payment_method_id"
                                                    value="{{ $paymentMethod->id }}" @if ($checkedPaymentMethodId == $paymentMethod->id) checked @endif required>
                                                <strong>{{ $paymentMethod->getTranslatedAttribute('name') }}</strong>
                                            </label>
                                            @if ($paymentMethod->code == 'cash' || $paymentMethod->code == 'payme' || $paymentMethod->code == 'click')
                                                <div class="alert alert-warning alert-dismissible d-none alert-payment-method-description alert-payment-method-description-{{ $paymentMethod->id }}">
                                                    <strong>{{ $paymentMethod->getTranslatedAttribute('name') }}.</strong>
                                                    {{ $paymentMethod->getTranslatedAttribute('description') }}
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                        @error('payment_method_id')
                                            <div class="invalid-feedback d-block" role="alert">
                                                <strong>{{ __('main.choose_value') }}</strong>
                                            </div>
                                        @enderror
                                    </div>

                                    @foreach ($paymentMethods as $paymentMethod)
                                        <div class="payment-method-additional payment-method-additional-{{ $paymentMethod->id }} d-none">
                                            <div class="my-4">
                                                @if ($paymentMethod->code == 'alifshop')
                                                    <div class="p-3 rounded bg-light border">

                                                        <div class="mb-2">
                                                            <img src="{{ asset('images/partners/alifnasiya.svg') }}" alt="Alifnasiya" class="img-fluid" width="150">
                                                        </div>

                                                        <input type="hidden" name="installment_payment_months" value="{{ $partnerInstallmentDuration }}">
                                                        <input type="hidden" name="total_price_per_month" value="{{ $totalPricePerMonth }}">

                                                        <div class="alifshop-first-step-block @if (session()->has('alifshop_otp_sent')) d-none @endif">
                                                            <div class="mb-2">
                                                                <h4>{{ __('main.alifshop_azo_login') }}</h4>
                                                                <p>{{ __('main.alifshop_azo_login_description') }}</p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('main.form.phone_number') }}</label>
                                                                <input type="text" name="alifshop_phone_number"
                                                                    class="form-control payment-method-additional-required  payment-method-additional-required-{{ \App\Order::PAYMENT_METHOD_ALIFSHOP }} phone-input-mask @error('alifshop_phone_number') is-invalid @enderror "
                                                                    value="{{ session()->has('alifshop_phone_number') ? session()->get('alifshop_phone_number') : old('alifshop_phone_number', '') }}"
                                                                    pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                                                                @error('alifshop_phone_number')
                                                                    <div class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </div>
                                                                @enderror
                                                                @error('alifshop_otp')
                                                                    <div class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ __('main.alifshop_enter_account_error') }}</strong>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-sm btn-primary alifshop-login-btn">{{ __('main.nav.login') }}</button>
                                                                <a href="https://alifnasiya.uz/auth/registration" target="_blank" class="btn btn-sm btn-primary alifshop-register-btn" data-attempt-url="{{ route('order.attempt') }}"
                                                                style="background-color: #F98329; border: 1px solid #F98329">{{ __('main.nav.register') }}</a>
                                                            </div>
                                                        </div>

                                                        <div class="alifshop-second-step-block @if (!session()->has('alifshop_otp_sent')) d-none @endif">
                                                            <div class="mb-2">
                                                                <h4>{{ __('main.sms_code_verification') }}</h4>
                                                                <p>{{ __('main.alifshop_sms_verification_has_been_sent_to_your_phone') }}</p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('main.sms_code') }}</label>
                                                                <input type="text" name="alifshop_otp" class="form-control @error('alifshop_otp') is-invalid @enderror" value="">
                                                                @error('alifshop_otp')
                                                                    <div class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ __('main.incorrect_code') }}</strong>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-2">
                                                                <span>{{ session()->get('alifshop_phone_number') }}</span>
                                                                <a href="javascript:;" class="alifshop-change-phone-number-btn text-primary">{{ __('main.change_phone_number') }}</a>
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach



                                    {{-- <div class="form-group">
                                        @php
                                        $checkedShippingMethodID = old('shipping_method_id') ?: 1; // default = 1 - Standard
                                        @endphp
                                        @foreach ($shippingMethods as $shippingMethod)
                                        <label class="radio-label">
                                            <input id="shipping_method_{{ $shippingMethod->id }}" type="radio"
                                                name="shipping_method_id" value="{{ $shippingMethod->id }}"
                                                @if ($checkedShippingMethodID == $shippingMethod->id) checked @endif required>
                                            <strong>{{ $shippingMethod->name }}</strong>
                                        </label>
                                        @endforeach
                                        @error('shipping_method_id')
                                        <div class="invalid-feedback d-block" role="alert">
                                            <strong>{{ __('main.choose_value') }}</strong>
                                        </div>
                                        @enderror
                                    </div> --}}
                                    <div>
                                        <!--  pattern="\d{4} \d{4} \d{4} \d{4}"   pattern="\d{2}/\d{2}" -->
                                        <div class="row form-group d-none" id="card_get_form">
                                            <div class="col-8">
                                                <h4 class="mb-4">{{ __('main.your_card') }}</h4>
                                                <input type="text" name="card_number" class="form-control card2-input-mask">
                                            </div>
                                            <div class="col-4">
                                                <h4 class="mb-4">{{ __('main.your_card_val') }}</h4>
                                                <input type="text" name="card_validation_date" class="form-control card-available2-input-mask">
                                            </div>
                                        </div>

                                        @if (!$checkoutAvailable)
                                            <div class="text-danger my-2">{{ __('main.checkout_not_available') }} <a href="{{ route('cart.index') }}">{{ __('main.go_to_cart') }}</a></div>
                                        @endif
                                        <div>
                                            <button type="submit" class="theme-btn place-order-btn d-none d-xl-block radius-6 @if (!$checkoutAvailable) disabled @endif"
                                                @if (!$checkoutAvailable) disabled @endif>{{ __('main.place_order') }}</button>

                                            {{-- Fixed button --}}
                                            <div class="product-page-nav-bottom nav-bottom d-lg-none">
                                                <div class="nav-bottom-buttons row">
                                                    <div class="nav-bottom-buttons__item col flex-grow-1">
                                                        <button type="submit"
                                                                class="theme-btn radius-6  @if(!$checkoutAvailable) disabled @endif"
                                                                @if(!$checkoutAvailable) disabled @endif
                                                                style="background-color: #F98329; font-size:16px; padding: 7px; display: block; width: 100%;"
                                                        >
                                                            </i> {{ __('main.place_order') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3"></div>
                        </form>
                    @else
                        <div class="my-5 lead text-center">{{ __('main.cart_is_empty') }}</div>
                    @endif
                </div>
                {{-- 
                @guest
                    <div class="container pt-2">
                        <div class="alert alert-warning alert-dismissible fade show" id="jquery_payment_description_cash" role="alert">
                            {!! __('main.order_unregistered_info') !!}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endguest
                --}}
            </section>
        </main>
    @endif
@endsection

@section('after_footer_blocks')
    <!-- INSTALLMENT PAYMENT MODAL -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ __('main.bnpl') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 0px 12px!important;">
                    @if (!empty($partnersPrices))
                        <div class="partner-installments-list my-4">
                            @php
                                $ii = 1;
                            @endphp
                            @foreach ($partnersPrices as $item)
                                @if (empty($item['prices']))
                                    @continue
                                @endif
                                {{-- @if ($item['partner']->id == 2 && (!auth()->check() || auth()->user()->id != 335))
                            @continue
                        @endif --}}
                                @php
                                    $firstItemPrice = $item['prices'][0];
                                @endphp

                                {{-- INTALLMENT STARTS --}}
                                @if (!empty($category) && $category->id == 121)
                                    @if($item['partner']->id != 1)
                                        <div class="row radius-14 mb-1 py-2 px-1 font-weight-bold partner-installment-block partner-installment-block-{{ $item['partner']->id }}"
                                            style="border: none; box-shadow: none; padding-bottom: 0 !important; padding-top: 0 !important;">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-4"></div>
                                                    <div class="col-4 mb-1 d-flex align-items-center justify-content-center">
                                                        <img src="{{ $item['partner']->img }}" alt="{{ $item['partner']->getTranslatedAttribute('name') }}"
                                                            class="img-fluid partner-installment-block-img">
                                                    </div>
                                                    <div class="col-4"></div>
                                                </div>

                                                <div class="row d-flex align-items-center justify-content-center flex-wrap">
                                                    <div class="col-6 d-flex align-items-center justify-content-end text-right">
                                                        <span
                                                            class="d-inline-block bg-yellow px-3 py-2 radius-20 text-nowrap partner-installment-price my-1 mx-2 ">{{ $firstItemPrice['price_per_month_formatted'] }}</span>
                                                    </div>
                                                    <div class="col-6 d-flex align-items-center justify-content-start text-left">
                                                        <select name="partner_installment"
                                                            class="form-control form-control-sm text-lowercase my-1 mx-2 select-partner-installment-{{ $item['partner']->id }} select-partner-installment"
                                                            style="width: 118px;">
                                                            @foreach ($item['prices'] as $itemPrice)
                                                                <option value="{{ $itemPrice['partner_installment']->id }}"
                                                                    data-duration="{{ $itemPrice['partner_installment']->duration }}"
                                                                    data-price="{{ $itemPrice['price'] }}" data-price-formatted="{{ $itemPrice['price_formatted'] }}"
                                                                    data-price-per-month="{{ $itemPrice['price_per_month'] }}"
                                                                    data-price-per-month-formatted="{{ $itemPrice['price_per_month_formatted'] }}"
                                                                    data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $itemPrice['partner_installment']->id]) }}">
                                                                    {{ $itemPrice['partner_installment']->duration }}
                                                                    {{ trans_choice('main.month3', $itemPrice['partner_installment']->duration % 20) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-sm-12">
                                                        <button type="button" class="theme-btn d-inline-flex radius-6 add-to-cart-btn"
                                                            style="background-color: #F98329; font-size:16px; padding: 7px; display: block; width: 100%;"
                                                            data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $firstItemPrice['partner_installment']->id]) }}"
                                                            data-id="{{ $product->id }}"
                                                            data-name="{{ $product->name }}"
                                                            data-price="{{ $product->current_price }}"
                                                            data-quantity="1">
                                                            {{ __('main.installment_payment_buy') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="row radius-14 mb-1 py-2 px-1 font-weight-bold partner-installment-block partner-installment-block-{{ $item['partner']->id }}"
                                        style="border: none; box-shadow: none; padding-bottom: 0 !important; padding-top: 0 !important;">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-4"></div>
                                                <div class="col-4 mb-1 d-flex align-items-center justify-content-center">
                                                    <img src="{{ $item['partner']->img }}" alt="{{ $item['partner']->getTranslatedAttribute('name') }}"
                                                        class="img-fluid partner-installment-block-img">
                                                </div>
                                                <div class="col-4"></div>
                                            </div>

                                            <div class="row d-flex align-items-center justify-content-center flex-wrap">
                                                <div class="col-6 d-flex align-items-center justify-content-end text-right">
                                                    <span
                                                        class="d-inline-block bg-yellow px-3 py-2 radius-20 text-nowrap partner-installment-price my-1 mx-2 ">{{ $firstItemPrice['price_per_month_formatted'] }}</span>
                                                </div>
                                                <div class="col-6 d-flex align-items-center justify-content-start text-left">
                                                    <select name="partner_installment"
                                                        class="form-control form-control-sm text-lowercase my-1 mx-2 select-partner-installment-{{ $item['partner']->id }} select-partner-installment"
                                                        style="width: 118px;">
                                                        @foreach ($item['prices'] as $itemPrice)
                                                            <option value="{{ $itemPrice['partner_installment']->id }}"
                                                                data-duration="{{ $itemPrice['partner_installment']->duration }}"
                                                                data-price="{{ $itemPrice['price'] }}" data-price-formatted="{{ $itemPrice['price_formatted'] }}"
                                                                data-price-per-month="{{ $itemPrice['price_per_month'] }}"
                                                                data-price-per-month-formatted="{{ $itemPrice['price_per_month_formatted'] }}"
                                                                data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $itemPrice['partner_installment']->id]) }}">
                                                                {{ $itemPrice['partner_installment']->duration }}
                                                                {{ trans_choice('main.month3', $itemPrice['partner_installment']->duration % 20) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-sm-12">
                                                    <button type="button" class="theme-btn d-inline-flex radius-6 add-to-cart-btn"
                                                        style="background-color: #F98329; font-size:16px; padding: 7px; display: block; width: 100%;"
                                                        data-checkout-url="{{ route('cart.checkout', ['product-id' => $product->id, 'partner-installment-id' => $firstItemPrice['partner_installment']->id]) }}"
                                                        data-id="{{ $product->id }}"
                                                        data-name="{{ $product->name }}"
                                                        data-price="{{ $product->current_price }}"
                                                        data-quantity="1">
                                                        {{ __('main.installment_payment_buy') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- INTALLMENT ENDS --}}

                                @if ($ii == 1 || $ii == 2)
                                    <hr>
                                @endif

                                @php
                                    $ii++;
                                @endphp
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- INSTALLMENT PAYMENT MODAL ends -->
@endsection

@section('scripts')
<script src="{{ asset('js/confetti.js') }}"></script>
    <script>
        $('#current_url_input').val(window.location.href);
        var payment_id = $('input[name="payment_method_id"]:checked').val();
        if (payment_id == 1) {
            $('.passport_second_form').hide();
        }

        if($(".card_anor").val()){
            $(".card_validation_button").removeAttr("disabled");
            $(".card_validation_button").removeClass("disabled");
        }

        setInterval(() => {
            $("#card_validation_ask_otp").fadeIn(1000).fadeOut(1000);
        }, 1000);

        $('.card_anor').on('keyup', function(){
            var value = $(this).val();
            value = value.slice( 0, 4);
            var html = $('#card_validation_text');
            var html2 = $('#card_validation_text2');
            var buttonCard = $(".card_validation_button");
            console.log("9860 6066");
            console.log(value);
            if(value == "9860"){
                html2.removeClass("text-danger");
                html2.addClass('text-danger').text("");
                html.addClass('text-success').text("Правильный формат.");
                buttonCard.removeAttr("disabled");
                buttonCard.removeClass("disabled");
                
            }else{
                html.removeClass("text-success");
                html.addClass('text-success').text("");
                html2.addClass('text-danger').text("Правильный формат начинается с 9860");
                buttonCard.addAttr("disabled");
                buttonCard.addClass("disabled");
            }
        });

        if (localStorage.getItem('setStorageName')) {
            var finalName = localStorage.getItem('setStorageName').replace('"', ""); 
            $("#storage_name").val(finalName);
        }

        if (localStorage.getItem('setStoragePhone')) {
            var finalPhone = localStorage.getItem('setStoragePhone').replace('"', "");
            $("#storage_phone").val(finalPhone);
        }

        $('#storage_name').keyup(function(){
            var storageName = $('#storage_name').val();
            localStorage.setItem('setStorageName', storageName);
            // console.log(localStorage.getItem('setStorageName'));
        });

        $('#storage_phone').keyup(function(){
            var storagePhone = $('#storage_phone').val();
            localStorage.setItem('setStoragePhone', JSON.stringify(storagePhone));
            // console.log(localStorage.getItem('setStoragePhone'));
        });

        // $(document).ready(function () {
        //     $("#payment_method_11").on('click', function () {
        //     });
        // });

        document.addEventListener('DOMContentLoaded', function() {

            let orderTotal = {{ $totalPrice }};

            // location
            let form = $('.checkout-form');
            let locationText = $('#location-text');
            $('.get-location-btn').on('click', function(e) {
                e.preventDefault();
                getLocation();

                function getLocation() {
                    locationText.html(spinnerHTML()).addClass('mb-2');
                    if (navigator.geolocation) {
                        let getOptions = {
                            maximumAge: 10000,
                            timeout: 5000,
                            enableHighAccuracy: true
                        };
                        navigator.geolocation.getCurrentPosition(geoSuccess, geoError, getOptions);
                    } else {
                        locationText.text("{{ __('main.geolocation_is_not_supported_by_browser') }}");
                    }
                }

                function geoError(error) {
                    // console.log(error);
                    locationText.text("{{ __('main.failed_to_determine_geolocation') }}");
                }

                function geoSuccess(position) {
                    // console.log(position);
                    locationText.text(" ");
                    // locationText.text("{{ __('main.latitude') }}: " + position.coords.latitude + "; {{ __('main.longitude') }}: " + position.coords.longitude);
                    form.find('[name="latitude"]').val(position.coords.latitude);
                    form.find('[name="longitude"]').val(position.coords.longitude);
                    form.find('[name="location_accuracy"]').val(position.coords.accuracy);

                    console.log('latitude: ' + position.coords.latitude, 'longitude: ' + position.coords.longitude);
                    // let googleMapURL = "https://maps.google.com/maps?q="+position.coords.latitude+","+position.coords.longitude+"&hl=es&z=14&amp;output=embed";
                    /* form.find('[name="mapImage"]').attr('src', googleMapURL); */
                    $('.iframe-div').html('<iframe src="https://maps.google.com/maps?q='+position.coords.latitude+','+position.coords.longitude+'&hl=es;z=14&amp;output=embed" width="100%" height="400" frameborder="0" style="border:0"></iframe>');
                    // form.find('[name="mapImage"]').addClass('d-block');
                }
            });

            // errors
            function showError(elem, errors) {
                let formGroup = elem.closest('.form-group');
                let elemName = elem.attr('name');

                // clean form group
                formGroup.find('.invalid-feedback').remove();
                elem.removeClass('is-invalid');

                // show error
                if (errors[elemName] != undefined) {
                    // has error
                    elem.addClass('is-invalid');
                    formGroup.append(`<div class="invalid-feedback d-block">${errors[elemName][0]}</div>`);
                }
            }

            function hideError(elem) {
                let formGroup = elem.closest('.form-group');
                let elemName = elem.attr('name');
                formGroup.find('.invalid-feedback').remove();
                elem.removeClass('is-invalid');
            }

            function showErrors(errors) {
                button.addClass('disabled');
                for (let i in errors) {
                    let elem = form.find('[name="' + i + '"]');
                    showError(elem, errors);
                }
            }

            // payment method
            function showPaymentMethodAdditional() {
                let paymentMethodId = $('.payment-method-input:checked').val();
                $('.payment-method-additional').addClass('d-none');
                $('.payment-method-additional-' + paymentMethodId).removeClass('d-none');
                $('.payment-method-additional-required').prop('required', false);
                $('.payment-method-additional-required-' + paymentMethodId).prop('required', true);
            }

            function showPaymentMethodDescription() {
                let paymentMethodId = $('.payment-method-input:checked').val();
                $('.alert-payment-method-description').addClass('d-none');
                $('.alert-payment-method-description-' + paymentMethodId).removeClass('d-none');
            }
            showPaymentMethodAdditional();
            showPaymentMethodDescription();
            $('.payment-method-input').on('change', function() {
                showPaymentMethodAdditional();
                showPaymentMethodDescription();
            });

            // place order btn
            $('#checkout-form').on('submit', function(e) {
                let btn = $(this).find('[type="submit"]');
                btn
                    .addClass('disabled')
                    .append(
                        '<span class="spinner mx-1"><svg class="svg-spinner" width="24" height="24" viewBox="0 0 50 50"><circle class="svg-spinner-path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg></span>'
                        )
                // .prop('disabled', true);

                // YANDEX METRICS
                var yandexEcompId = $('#yandex-ecom-product-id').val();
                var yandexEcomName = $('#yandex-ecom-product-name').val();
                var yandexEcompPrice = $('#yandex-ecom-product-price').val();
                var yandexEcomQuantity = $('#yandex-ecom-product-quantity').val();

                dataLayer.push({
                    "ecommerce": {
                        "currencyCode": "UZS",
                        "purchase": {
                            "actionField": {
                                "id": "TRX987"
                            },
                            "products": [{
                                "id": yandexEcompId,
                                "name": yandexEcomName,
                                "price": yandexEcompPrice,
                                "quantity": yandexEcomQuantity
                            }]
                        }
                    }
                });

                ym(84743134, 'reachGoal', 'oformit-zakaz');
                return true
                // YANDEX ENDS

                // GOOGLE ANALYTICS
                gtag('event', 'purchase', {
                    affiliation: 'Google Store',
                    currency: 'UZS',
                    items: [{
                        item_id: yandexEcompId,
                        item_name: yandexEcomName,
                        coupon: yandexEcomName,
                        affiliation: 'Google Store',
                        price: yandexEcompPrice,
                        currency: 'UZS',
                        quantity: yandexEcomQuantity
                    }],
                    transaction_id: 'T_12345'
                });

                gtag('event', 'click', {
                    event_category: 'oformit-zakaz'
                });
                // GOOGLE ENDS
            });

            // alifshop azo alif nasiya
            $('.alifshop-register-btn').on('click', function(e) {
                // e.preventDefault();
                // let btn = $(this);
                let elem = $('[name="alifshop_phone_number"]');
                hideError(elem);
            });
            $('.alifshop-login-btn').on('click', function(e) {
                e.preventDefault();
                let btn = $(this);
                let elem = $('[name="alifshop_phone_number"]');
                if (btn.prop('disabled')) {
                    return;
                }
                let alifPhone = elem.val().replace(/[^\d]/g, '');
                if (!/^998\d{9}$/.test(alifPhone)) {
                    let errors = {
                        alifshop_phone_number: [
                            '{{ __('main.incorrect_phone_number') }}'
                        ],
                    }
                    showError(elem, errors)
                    return false;
                }

                $.ajax({
                        url: '{{ route('alifshop.azo.clients.check') }}',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            phone_number: alifPhone,
                            order_total: orderTotal
                        },
                        beforeSend: function() {
                            btn.addClass('disabled').prop('disabled', true).append('<span class="spinner"><i class="fa fa-circle-o-notch fa-spin ml-1"></i></span>');
                        }
                    })
                    .done(function(data) {
                        $('.alifshop-first-step-block').addClass('d-none');
                        $('.alifshop-second-step-block').removeClass('d-none');
                    })
                    .fail(function(data) {
                        // console.log(data);
                        if (data.status == 404) {
                            let errors = {
                                alifshop_phone_number: [
                                    data.responseJSON.message,
                                ],
                            }
                            showError(elem, errors);
                            $('.alifshop-register-btn').removeClass('d-none');
                        } else if (data.status == 460) {
                            let errors = {
                                alifshop_phone_number: [
                                    data.responseJSON.message,
                                ],
                            }
                            showError(elem, errors);
                        }
                    })
                    .always(function(data) {
                        btn.removeClass('disabled').prop('disabled', false).find('.spinner').remove();
                    });
            });
            $('.alifshop-change-phone-number-btn').on('click', function(e) {
                $('.alifshop-first-step-block').removeClass('d-none');
                $('.alifshop-second-step-block').addClass('d-none');
            });
            $('.alifshop-register-btn').on('click', function() {
                let url = $(this).data('attempt-url');
                let form = $('#checkout-form');
                $.ajax({
                    method: 'post',
                    url: url,
                    dataType: "json",
                    data: form.serialize()
                });
            });

            $("#card_get_form").hide();

            // bnpl_question_1_button
            $('.bnpl_question_1_button').on('click', function(){
                $('.bnpl_question_1').toggleClass('d-block', 'd-none');
            });
            $('.bnpl_question_2_button').on('click', function(){
                $('.bnpl_question_2').toggleClass('d-block', 'd-none');
            });

            var my_payment_method_id = $('.payment-method-input:checked').val();
            if (my_payment_method_id == 1) {
                $('#hideHasWorkPermitBlock').addClass('d-none');
            }

            // installment partners
            if ($('.select-partner-installment').length) {
                // applyPartnerInstallment($('.select-partner-installment').eq(0));
                $('body').on('change', '.select-partner-installment', function() {
                    applyPartnerInstallment($(this));
                });
                $('body').on('click', '.select-partner-installment option', function() {
                    applyPartnerInstallment($(this).parent());
                });
                // $('.partner-installment-block').on('click', function(){
                //     applyPartnerInstallment($(this).find('.select-partner-installment'));
                // })
                function applyPartnerInstallment(selectObj) {
                    let selectedOption = selectObj.find('option:selected');
                    let partnerBlock = selectObj.closest('.partner-installment-block');
                    let partnersListBlock = selectObj.closest('.partner-installments-list');
                    // let partnerAddToCartBtn = partnersListBlock.find('.add-to-cart-btn');
                    let partnerAddToCartBtn = partnerBlock.find('.add-to-cart-btn');
                    partnerAddToCartBtn.attr('data-checkout-url', selectedOption.data('checkout-url'));
                    if ($('.product-page-add-to-cart-btn').hasClass('disabled')) {
                        partnerAddToCartBtn.addClass('disabled').prop('disabled', true);
                    }
                    partnerBlock.find('.partner-installment-price').text(selectedOption.data('price-per-month-formatted'));
                    // $('.partner-installment-block').removeClass('active');
                    // partnerBlock.addClass('active');
                }
            }

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
