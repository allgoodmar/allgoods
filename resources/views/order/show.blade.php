@extends('layouts.app')

@section('seo_title', __('main.order'))
@section('meta_description', '')
@section('meta_keywords', '')

@section('content')

    <main class="main">

        <section class="content-header">
            <div class="container">
                @include('partials.breadcrumbs')
            </div>
        </section>

        {{-- @include('partials.alerts') --}}

        <div class="container pt-2">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    <h4 class="m-0">{{-- {{ session()->get('success') }}. <br> --}} {{ __('main.status') }}: {{ $order->status_title }}. </h4>
                </div> 
            @endif

            <div class="alert alert-warning alert-dismissible fade show" id="jquery_payment_description_cash" role="alert">
                {!! __('main.order_accepted_info') !!}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
        </div>

        <div class="container" style="color: #F6954A;">
            <i class="fa fa-phone" style="widows: 28px; height: 28px;"></i> <a href="tel:+998 78 777-90-90" style="color: #F6954A;"> Связаться с оператором. </a>
        </div>

        <div class="container py-4 py-lg-5">

            {{-- @auth
                <div class="mb-5 d-none d-lg-block">
                    <a href="{{ route('profile.orders') }}">
                        <strong> &larr; {{ __('main.back_to_orders') }}</strong>
                    </a>
                </div>
            @endauth --}}

            <h1>{{ __('main.view_order') }}</h1>

            {{-- <div class="mb-4">
                @auth
                    <a href="{{ route('profile.orders') }}" class="btn btn-primary">
                        <i class="fa fa-angle-left mr-2"></i>
                        {{ __('main.back_to_orders') }}
                    </a>
                @endauth
                <a href="{{ route('order.print', ['order' => $order->id, 'check' => md5($order->created_at)]) }}" class="btn btn-link" target="_blank">
                    <i class="text-dark fa fa-print mr-2"></i>
                    <span class="text-dark">{{ __('main.print_version') }}</span>
                </a>
            </div> --}}

            <div class="box">
                <h3 class="box-header">
                    {{ __('main.order') }} #{{ $order->id }}
                </h3>

                <p>
                    {{ $order->payment_method_title }}
                </p>

                @if ($order->payment_method_id == \App\Order::PAYMENT_METHOD_UZUM)
                    @if(session()->get('uzum_contract'))
                        <a href="{{ session()->get('uzum_contract') }}" class="btn btn-success"> <i class="bi bi-download"></i> Скачать договор</a>
                    @endif
                @endif

                @if (!$order->isInstallmentOrder())
                    @include('partials.order_status')
                @endif

                @if($order->isPending())
                    <div class="mb-4">
                        @if($order->payment_method_id == \App\Order::PAYMENT_METHOD_PAYME)
                            <form id="form-payme" method="POST" action="https://checkout.paycom.uz/">
                                <input type="hidden" name="merchant" value="{{ config('services.paycom.merchant_id') }}">
                                <input type="hidden" name="amount" value="{{ $order->total_tiyin }}">
                                <input type="hidden" name="account[order_id]" value="{{ $order->id }}">
                                <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                                <input type="hidden" name="currency" value="860">
                                <input type="hidden" name="callback" value="{{ $order->url }}">
                                <input type="hidden" name="callback_timeout" value="15">
                                <input type="hidden" name="description" value="{{ __('main.order') . ': ' . $order->id }}">
                                <input type="hidden" name="detail" value=""/>

                                <input type="hidden" name="button" data-type="svg" value="colored">
                                <div class="row">
                                    <div class="col-sm-8 col-md-6 col-lg-4 img-container">
                                        <div id="button-container" class="button-container payme-button-container"></div>
                                    </div>
                                </div>
                                <button type="submit" class="button d-none">{{ __('main.pay_with', ['operator' => 'Payme']) }}</button>
                            </form>
                            <script src="https://cdn.paycom.uz/integration/js/checkout.min.js"></script>
                            <script>
                                Paycom.Button('#form-payme', '#button-container')
                            </script>
                        @elseif($order->payment_method_id == \App\Order::PAYMENT_METHOD_CLICK)
                            <form id="click_form" action="https://my.click.uz/services/pay" method="get">
                                <input type="hidden" name="amount" value="{{ $order->total }}"/>
                                <input type="hidden" name="merchant_id" value="{{ config('services.click.merchant_id') }}"/>
                                <input type="hidden" name="merchant_user_id" value="{{ config('services.click.user_id') }}"/>
                                <input type="hidden" name="service_id" value="{{ config('services.click.service_id') }}"/>
                                <input type="hidden" name="transaction_param" value="{{ $order->id }}"/>
                                <input type="hidden" name="return_url" value="{{ $order->url }}"/>
                                {{--<input type="hidden" name="card_type" value="uzcard/humo"/>--}}
                                <button type="submit" class="click_logo"><i></i>{{ __('main.pay_with', ['operator' => 'CLICK']) }}</button>
                            </form>
                        @elseif($order->payment_method_id == \App\Order::PAYMENT_METHOD_INTEND)
                            <form id="intend_form" class="form-group qty" action="{{ env('INTEND_URL') }}" method="POST">
                                <input type="hidden" name="duration" value="12">
                                <input type="hidden" name="api_key" value="{{ env('INTEND_API_KEY') }}">    
                                <input type="hidden" name="redirect_url" value="{{ $order->url }}">
                                @foreach($order->orderItems as $key => $orderItem)
                                    <input type="hidden" name="products[{{ $key }}][id]" value="{{ $orderItem->id }}">
                                    <input type="hidden" name="products[{{ $key }}][name]" value="{{ $orderItem->name }}">
                                    <input type="hidden" name="products[{{ $key }}][price]" value="{{ intval($orderItem->price) }}">
                                    <input type="hidden" name="products[{{ $key }}][quantity]" value="{{ intval($orderItem->quantity) }}">
                                    <input type="hidden" name="products[{{ $key }}][sku]" value="product_{{ $orderItem->id }}">
                                    <input type="hidden" name="products[{{ $key }}][weight]" value="0">
                                @endforeach
                                <button type="submit" class="btn text-white fs-5" style="background: #188d7c!important;font-size: 20px;">
                                    Купить в рассрочку <img src="https://allgood.uz/img/intend/logo.jpg" alt="intend" style=" border-radius: 5px; ">
                                </button>
                            </form>
                        @endif
                    </div>
                @endif


                @if($order->payment_method_id == \App\Order::PAYMENT_METHOD_ZOODPAY_INSTALLMENTS)
                    <div class="my-4">
                        @if ($zoodpayTransaction && !empty($zoodpayTransaction->zoodpay_status))
                            <p>
                                Zoodpay Status:
                                {{ $zoodpayTransaction->zoodpay_status }}.
                                @if (!empty($zoodpayTransaction->zoodpay_error_message))
                                    {{ $zoodpayTransaction->zoodpay_error_message }}
                                @endif
                            </p>
                        @endif
                        @if (empty($zoodpayTransaction->zoodpay_status) || $zoodpayTransaction->zoodpay_status == 'In active')
                            <form id="zoodpay_form" action="{{ route('zoodpay.transaction.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <div class="zoodpay-button-container">
                                    {{-- <button type="submit" class="btn btn-lg btn-info">{{ __('main.pay_with', ['operator' => 'ZOODPAY']) }}</button> --}}
                                    <div>{{ __('main.pay_with', ['operator' => 'ZOODPAY']) }}:</div>
                                    <input type="image" src="{{ asset('images/payment/zoodpay/button-' . app()->getLocale() . '.jpg') }}" alt="{{ __('main.pay_with', ['operator' => 'ZOODPAY']) }}">
                                </div>
                            </form>
                        @endif
                    </div>
                @endif

                <div class="order_table table-responsive">
                    @if($order->payment_method_id == \App\Order::PAYMENT_METHOD_INTEND)
                        <h3>@lang('main.intend_pay_success')</h3>
                    @else
                        <table class="table products-list-table table-bordered">
                            <thead>
                            <tr class="bg-light">
                                <th class="border-bottom-0">{{ __('main.product') }}</th>
                                <th class="border-bottom-0">{{ __('main.price') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->orderItems as $orderItem)
                                <tr>
                                    <td>
                                        {{ $orderItem->name }}
                                        <strong> × {{ $orderItem->quantity }}</strong>
                                    </td>
                                    <td class="text-nowrap"> {{ Helper::formatPrice($orderItem->total) }} </td>
                                </tr>
                            @endforeach
                            @if (!empty(auth()->user()->coupon_sum))
                                @if (auth()->user()->is_coupon_used == 'yes')
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
                            <tfoot>
                            @if ($order->shipping_price > 0)
                            <tr class="order_total">
                                <td><strong>{{ __('main.delivery') }}</strong></td>
                                <td class="text-nowrap"><strong>{{ Helper::formatPrice($order->shipping_price) }}</strong></td>
                            </tr>
                            @endif
                            <tr class="order_total">
                                <td><h4 class="m-0">{{ __('main.total') }}</h4></td>
                                <td class="text-nowrap">    
                                    <h4 class="m-0 text-nowrap text-lowercase">
                                        @if ($order->isInstallmentOrder())
                                            {{ Helper::formatPrice($order->total_price_per_month) }} / {{ $order->installment_payment_months }} {{ __('main.month2') }}
                                        @else
                                            {{ Helper::formatPrice($order->total) }}
                                        @endif
                                    </h4> 
                                    {{--
                                    @if (!empty(auth()->user()->coupon_sum))
                                        @if (auth()->user()->is_coupon_used == 'yes')
                                            <span class="text-danger" style="font-weight: 0;">С купоном - {{ Helper::formatPrice(auth()->user()->coupon_sum) }}</span>
                                        @endif
                                    @endif --}}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    @endif
                </div>
            </div>
            <div class="pb-5"></div>
        </div>
    </main>
@endsection

@section('styles')
    <style>
        .click_logo {
            padding: 15px 35px 15px 20px;
            cursor: pointer;
            color: #fff;
            line-height: 60px;
            font-size: 24px;
            white-space: nowrap;
            font-family: Arial, sans-serif;
            font-weight: bold;
            text-align: center;
            border: 1px solid #343643;
            border-radius: 10px;
            background-color: #343643;
        }

        .click_logo i {
            background: url(/images/partners/click.png) no-repeat top left;
            background-size: contain;
            width: 60px;
            height: 60px;
            display: block;
            float: left;
        }

        .payme-button-container input[type="image"] {
            /* max-width: 200px; */
            max-width: 100%;
        }

        .zoodpay-button-container input[type="image"] {
            /* max-width: 280px; */
            max-width: 100%;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function () {
        @if($order->isPending())
            @if($order->payment_method_id == \App\Order::PAYMENT_METHOD_CLICK)
                $('#click_form').trigger('submit');
            @endif
        @endif
        @if($order->isPending())
            @if($order->payment_method_id == \App\Order::PAYMENT_METHOD_INTEND)
                $('#intend_form').trigger('submit');
            @endif
        @endif
        });

        var redirectToTelegram = "{{ $order->payment_method_id }}";
        /*
        if (redirectToTelegram == 1 || redirectToTelegram == 4 || redirectToTelegram == 5) {
            window.setTimeout(function(){
                window.location.href = "https://t.me/nasiyauz_bot";
            }, 10000);
        }*/
    </script>
@endsection
