@extends('layouts.app')

@section('content')

@php
    function add3dots($string, $repl, $limit)
    {
    if(strlen($string) > $limit)
    {
        return substr($string, 0, $limit) . $repl;
    }
    else
    {
        return $string;
    }
    }
@endphp

<div class="container">
    <div class="row">

    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="card my-2 shadow">
                        <div class="card-header">Индикаторы</div>
                        @php
                            $money = "5500000000.00";
                            $per = ($money / 100) * 5;
                        @endphp
                        <div class="card-body seller-aside">
                            <span>Оборот: <span class="badge bg-info text-white">{{ Helper::formatPrice($revenue) }}</span></span>

                            <br>
                            <span>Комиссия: <span class="badge bg-danger text-white">{{ Helper::formatPrice($percentage) }}</span></span>
                            <span class="badge bg-warning">-5%</span>

                            <br>
                            <span>Прибыль: <span class="badge bg-success text-white">{{ Helper::formatPrice($profit) }}</span></span>

                            <br>
                            <span>Количество заказов: <span class="badge bg-primary text-white" style="color: #F98329;">{{ $ordersCount }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card my-2 shadow">
                        <div class="card-header">Менью</div>

                        <div class="card-body seller-aside makeBiggerA">
                            <a href="{{ route('seller.account.products') }}"> <i class="bi bi-shop"></i> Товары</a>
                            <a href="{{ route('seller.update_form_index', ['id'=>$seller_company->id]) }}"> <i class="bi bi-wrench"></i> Настройки</a>
                            <hr>
                            <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Выйти</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card my-2 shadow">
                <div class="card-header">Последние заказы {{ Session::get('owner_id_login') }}</div>

                <div class="card-body p-0">
                    <input type="text" class="form-control" placeholder="Напишите ключовое слово..." id="myInput">
                    <table class="table table-sm table-striped">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Дата</th>
                            <th scope="col">Имя</th>
                            <th scope="col">Продукт</th>
                            <th scope="col">Количество</th>
                            <th scope="col">Сумма</th>
                            <th scope="col">Телефон</th>
                          </tr>
                        </thead>
                        <tbody id="myTable">
                            @forelse ($seller_orders as $item)

                            @php
                            if (!isset($size)) {
                                $size = 'small';
                            }
                            $showImg = $size . '_img';
                            $showSecondImg = 'second_' . $size . '_img';
                            @endphp

                            <tr>
                                <th scope="row">{{ $item->id }}</th>
                                <td>{{ date('d-m-Y H:i:s', strtotime($item->order_item_created_at)) }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ add3dots($item->product_name, "...", 50); }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->price }}</td>
                                <td><a href="tel:{{ $item->phone_number }}">{{ $item->phone_number }}</a></td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row">Нет продукции</th>
                            </tr>
                            @endforelse

                            @if ($ordersCount > 30)
                                <div class="ml-3 mt-3">
                                    {{ $seller_orders->links() }}
                                </div>
                            @endif
                        </tbody>
                      </table>
                        <div class="ml-3">
                            {{ $seller_orders->links() }}
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <style>
        .seller-aside a{
            display: block;
        }

        .makeBiggerA a{
            font-size: 18px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $( function(){
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endsection
