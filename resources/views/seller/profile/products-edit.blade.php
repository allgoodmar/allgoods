@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <a href="{{ route('seller.account.products') }}" class="btn btn-primary">Назад</a>
            <div class="card mb-5">
                <div class="card-header">Изменить продукт - Артикул: {{ $product->sku }}</div>

                <div class="card-body">
                    @if(session()->has('success') || session()->has('danger'))
                        <div class="alert
                        @if (session()->has('success'))
                        alert-success
                        @endif

                        @if (session()->has('danger'))
                        alert-danger
                        @endif
                        ">
                            {{ session()->get('danger') }}
                            {{ session()->get('success') }}
                        </div>
                    @endif

                    <form class="d-inline" method="POST" action="{{ route('seller.account.products.update', ['id'=>$product->id]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <span>Название</span>
                                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <span>Артикул</span>
                                <input type="text" name="sku" class="form-control" value="{{ $product->sku }}">
                            </div>
                            <div class="col-md-6">
                                <span>Штрих-код</span>
                                <input type="text" name="barcode" class="form-control" value="{{ $product->barcode }}">
                            </div>
                        </div>

                        <hr>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4 mx-auto">
                                        <img src="{{ $product->img }}" alt="img" class="img-fluid">
                                    </div>
                                </div>
                                <div class="custom-file">
                                    <label class="custom-file-label" for="customFile">Аватар продукта</label>
                                    <input type="file" name="image" class="custom-file-input" id="customFile">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    @foreach ($product->medium_imgs as $key => $meduimImg)
                                    <div class="col-md-3 mx-auto">
                                        <a href="{{ $product->imgs[$key] }}" target="_blank" class="d-block" data-fancybox="gallery">
                                            <img src="{{ $meduimImg }}" alt="img" class="img-fluid d-flex" style="width: 78px; height: 78px;">
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="custom-file mt-2">
                                    <label class="custom-file-label" for="customFile">Фотография продукта</label>
                                    <input type="file" name="images[]" class="custom-file-input" id="customFile" multiple>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <span>Цена</span>
                                <input type="text" name="price" class="form-control" value="{{ $product->price }}" required>
                            </div>
                            <div class="col-md-6">
                                <span>Цена со скидкой</span>
                                <input type="text" name="sale_price" id="sale" class="form-control" value="{{ $product->sale_price }}">
                            </div>
                        </div>

                        <div class="row mt-2" id="sale_end_date">
                            <hr>
                            <div class="col-md-6"> @if(!empty($before)) <span class="text-success"> Установлен </span> @else <span class="text-warning"> Установите </span> @endif
                                <span>Дата окончания скидки</span>
                                <input type="date" name="sale_end_date" class="form-control" value="{{ $before }}">
                            </div>
                            <div class="col-md-6"> @if(!empty($after)) <span class="text-success"> Установлен </span> @else <span class="text-warning"> Установите </span> @endif
                                <span>Время окончания скидки</span>
                                <input type="text" name="sale_end_time" class="form-control" value="{{ $after }}">
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <span>Страница активна</span>
                                <select name="status" class="form-control">
                                    <option value="1" @if($product->status == 1) selected @endif>Активна</option>
                                    <option value="0" @if($product->status == 0) selected @endif>Неактивна</option>
                                    <option value="2" @if($product->status == 2) selected @endif>В ожидании</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <span>В наличии</span>
                                <input type="number" name="quantity" class="form-control" value="{{ $product->in_stock }}">
                            </div>

                            {{--
                            <div class="col-md-3">
                                <span>Хит продаж</span>
                                <select name="is_bestseller" class="form-control">
                                    <option value="1" @if($product->is_bestseller == 1) selected @endif>Да</option>
                                    <option value="0" @if($product->is_bestseller == 0) selected @endif>Нет</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <span>Новый товар</span>
                                <select name="is_new" class="form-control">
                                    <option value="1" @if($product->is_new == 1) selected @endif>Да</option>
                                    <option value="0" @if($product->is_new == 0) selected @endif>Нет</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <span>Акция</span>
                                <select name="is_promotion" class="form-control">
                                    <option value="1" @if($product->is_promotion == 1) selected @endif>Да</option>
                                    <option value="0" @if($product->is_promotion == 0) selected @endif>Нет</option>
                                </select>
                            </div>
                            --}}
                        </div>

                        <div class="row">
                            <div class="col-md-6 mx-auto text-center">
                                <button type="submit" class="btn btn-success p-1 mt-3 align-baseline mx-auto text-center">Изменить</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <script>
        $( function(){
            var sale = $('#sale').val();

            if (sale == 0.00) {
                $('#sale_end_date').hide();
            }else{
                $('#sale_end_date').show();
            }

            $('#sale').on('click', function(){
                $('#sale_end_date').show(800);
            });
        });
    </script>
@endsection
