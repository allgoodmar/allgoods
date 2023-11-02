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

@section('page_title', 'API токены')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-upload"></i>
        API токены
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                @include('voyager::alerts')

                <div class="panel panel-bordered">
                    <div class="panel-heading p-4">
                        <h4 class="m-0">API токены</h4>
                    </div>
                    <div class="panel-body">

                        <div class="my-4">
                            <a href="{{ route('voyager.users.index') }}" class="btn btn-primary">Назад</a>
                        </div>

                        @if(session()->has('token'))
                            <div>Новый токен создан. Сохраните его</div>
                            <div class="p-4">
                                <strong>{{ session()->get('token') }}</strong>
                            </div>
                        @endif

                        <form action="{{ route('voyager.users.api_tokens.store', ['user' => $user->id]) }}" method="post">

                            @csrf

                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Создать новый токен</button>
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
