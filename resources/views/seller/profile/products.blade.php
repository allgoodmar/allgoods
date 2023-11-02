@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="card my-2 shadow">
                        <div class="card-header">Индикаторы</div>
                        <div class="card-body seller-aside">

                            <span>Количество продуктов: <span class="badge bg-success text-white">{{ $productCount }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card my-2 shadow">
                        <div class="card-header">Дейтвия</div>

                        <div class="card-body seller-aside">
                            <form action="{{ route('seller.voyager.import.products') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="custom-file mb-3">
                                <label class="custom-file-label" for="customFile">Загрузить Еxcel файл</label>
                                <input  type="file" name="products" class="custom-file-input" id="customFile" required>
                            </div>

                            <button type="submit" class="btn btn-success btn-sm">Добавить продукт</button>
                            <a href="javascript:;" id="show_template">Показать шаблон</a>
                            </form>


                            <div id="show_template_div" class="d-none">
                                <form action="{{ route('seller.export.products.download') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <button type="submit" class="btn btn-dark btn-sm mt-3" style="width: 100%">Скачать шаблон</button>
                                </form>
                            </div>

                            <hr>

                            <a href="{{ route('seller.account') }}" class="btn btn-primary">Назад</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card my-2 shadow">
                <div class="card-header">Продукты компании {{ session()->get('seller_company_name') }}</div>

                <div class="card-body p-0">
                    <table class="table table-sm table-striped">
                        <input type="text" class="form-control" placeholder="Напишите ключовое слово..." id="myInput">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Фото</th>
                            <th scope="col">Название</th>
                            <th scope="col">Артикул</th>
                            <th scope="col">В наличии</th>
                            <th scope="col">Страница активна</th>
                            <th scope="col">Действия</th>
                          </tr>
                        </thead>
                        <tbody id="myTable">
                            @forelse ($products as $item)

                            @php
                            if (!isset($size)) {
                                $size = 'small';
                            }
                            $showImg = $size . '_img';
                            $showSecondImg = 'second_' . $size . '_img';
                            @endphp

                            <tr>
                                <th scope="row">{{ $item->id }}</th>
                                <td>
                                    <img src="{{ $item->$showImg }}" width="60px;" height="60px;" class="img-fluid" alt="">
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->sku }}</td>
                                <td>{{ $item->in_stock }}</td>
                                <td>
                                    <input type="radio" name="status_{{ $item->id }}" class="status-yes" value="{{ $item->id }}" @if($item->status == 1) checked @endif> <span class="status-yes-text-{{ $item->id }} @if($item->status == 1) text-success @endif">Да</span>
                                    <input type="radio" name="status_{{ $item->id }}" class="status-no"  value="{{ $item->id }}" @if($item->status == 0) checked @endif> <span class="status-no-text-{{ $item->id }} @if($item->status == 0) text-danger @endif">Нет</span>
                                </td>
                                <td class="d-flex justify-content-between">
                                    <a href="{{ $item->url }}" target="_blank" class="btn btn-info btn-sm" id="show_template" title="Смотреть"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('seller.account.products.edit', ['id'=>$item->id]) }}"  class="btn btn-primary btn-sm" id="show_template" title="Изменить"><i class="bi bi-pen"></i></a>
                                    <form action="{{ route('seller.export.products.download') }}" method="POST" enctype="multipart/form-data" class="ml-2">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm d-flex" title="Удалить" onclick="confirm('Подтверждайте действие')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row">Нет продукции</th>
                            </tr>
                            @endforelse

                            @if ($productCount > 30)
                                <div class="ml-3 mt-3">
                                    {{ $products->links() }}
                                </div>
                            @endif
                        </tbody>
                      </table>
                        <div class="ml-3">
                            {{ $products->links() }}
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <script>
        $( function(){
            $('#show_template').on('click', function(){
                $('#show_template_div').toggleClass('d-block d-none');
                $("#show_template").text($(this).text() == 'Показать шаблон' ? 'Скрыть шаблон' : 'Показать шаблон');
            });

            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            $(".status-yes").on('click', function(){
                var id = $(this).val();
                console.log(id);
                $.ajax({
                    type: "post",
                    url: "/seller/changestatustoyes",
                    data: {'id':id},
                    success: function (data) {
                        console.log(data);
                        $('.status-no-text-'+id).removeClass("text-danger");
                        $('.status-yes-text-'+id).addClass("text-success");
                    }
                }).fail(function(data) {
                    console.log("Failed");
                });
            });

            $(".status-no").on('click', function(){
                var id = $(this).val();
                console.log(id);
                $.ajax({
                    type: "post",
                    url: "/seller/changestatustono",
                    data: {'id':id},
                    success: function (data) {
                        console.log(data);
                        $('.status-yes-text-'+id).removeClass("text-success");
                        $('.status-no-text-'+id).addClass("text-danger");
                    }
                }).fail(function(data) {
                    console.log("Failed");
                });
            });
        });
    </script>
@endsection
