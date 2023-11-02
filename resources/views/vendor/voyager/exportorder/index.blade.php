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

@section('page_title', 'Экспорт')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-download"></i>
        Экспорт Заказов
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                @include('voyager::alerts')

                <div class="panel panel-bordered">
                    <div class="panel-heading p-4">
                        <h4 class="m-0">Экспорт Заказов</h4>
                    </div>
                    <div class="panel-body">
                        <div class="mb-4">
                            <h3>Сначала создайте файл для скачивания или сразу можете скачать последний созданный файл</h3>
                            <div>
                                <a href="{{ route('voyager.exportorders.products.store') }}" class="btn btn-lg btn-primary">Создать файл для скачивания</a>
                            </div>
                        </div>

                        <form action="{{ route('voyager.exportorders.products.download') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <button class="btn btn-success" type="submit">Скачать последний созданный файл</button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>

@stop

@section('javascript')
    <script></script>
@stop
