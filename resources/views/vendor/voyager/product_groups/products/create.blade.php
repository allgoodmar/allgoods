@extends('voyager::master')

@section('page_title', 'Группа товаров')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-settings"></i> Добавить продукт в группу {{ $productGroup->name }}
        <a href="{{ route('voyager.product_groups.settings', [$productGroup->id]) }}" class="btn btn-warning">
            Вернуться в группу
        </a>
    </h1>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Добавить продукт</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <p>Обязательные атрибуты продукта: {{ implode(', ', $productGroup->attributes->pluck('name')->toArray()) }}</p>
                        <form action="{{ route('voyager.product_groups.products.store', [$productGroup->id]) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="form_id">ID продукта *</label>
                                <input type="text" name="id" class="form-control" value="{{ old('id') }}" required>
                                @error('id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @error('attributes')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Добавить</button>
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
        //
    </script>
@stop
