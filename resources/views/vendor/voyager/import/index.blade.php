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

@section('page_title', 'Импорт')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-upload"></i>
        Импорт
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                @include('voyager::alerts')

                <div class="panel panel-bordered">
                    <div class="panel-heading p-4">
                        <h4 class="m-0">Продукция</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('voyager.import.products') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Выберите файл</label>
                                <input type="file" name="products" required>
                                @error('products')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Загрузить</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- <div class="panel panel-bordered">
                    <div class="panel-heading p-4">
                        <h4 class="m-0">Импорт товаров Smartup</h4>
                    </div>
                    <div class="panel-body">

                        @if (session()->has('result'))
                            <div style="margin: 15px 0;">
                                {!! session()->get('result') !!}
                            </div>
                        @endif

                        <form action="{{ route('voyager.import.smartup.products') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Импорт</button>
                            </div>
                        </form>
                    </div>
                </div> --}}

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
