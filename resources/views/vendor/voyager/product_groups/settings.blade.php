@extends('voyager::master')

@section('page_title', 'Группа товаров')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-settings"></i> Группа товаров {{ $productGroup->name }}
        <a href="{{ route('voyager.product_groups.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            {{ __('voyager::generic.return_to_list') }}
        </a>
    </h1>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Атрибуты</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <form action="{{ route('voyager.product_groups.attributes.update', [$productGroup->id]) }}" method="post">
                            @method('PUT')
                            @csrf
                            @foreach ($productGroup->attributes as $attribute)
                            <div class="row mb-10">
                                <div class="col-md-2 mb-5">
                                    {{ $attribute->name }}
                                </div>
                                <div class="col-md-4 mb-5">
                                    <select name="attributes[{{ $attribute->id }}][type]" class="form-control">
                                        @foreach (\App\ProductGroup::attributeTypes() as $key => $value)
                                            <option value="{{ $key }}" @if($attribute->pivot->type == $key) selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endforeach
                            <div>
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Значения атрибутов</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <form action="{{ route('voyager.product_groups.attribute_values.update', [$productGroup->id]) }}" method="post" enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            @foreach ($productGroup->attributeValues as $attributeValue)
                            <div class="row mb-10">
                                <div class="col-md-2 mb-5">
                                    {{ $attributeValue->name }}
                                </div>
                                <div class="col-md-4 mb-5">
                                    @if ($attributeValue->pivot->image)
                                        <div class="mb-5">
                                            <img src="{{ Voyager::image($attributeValue->pivot->image) }}" alt="" width="100" height="100">
                                        </div>
                                    @endif
                                    <input type="file" name="attribute_values[{{ $attributeValue->id }}][image]">
                                    @error('attribute_values.' . $attributeValue->id . '.image') <div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            @endforeach
                            <div>
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Продукция</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <div class="mb-10">
                            <a href="{{ route('voyager.product_groups.products.create', [$productGroup->id]) }}" class="btn btn-primary btn-sm">Добавить продукт в группу</a>
                        </div>

                        <div class="table-responsive mb-10">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 1%">ID</th>
                                    <th>Название</th>
                                    <th>Атрибуты</th>
                                    <th style="width: 1%"></th>
                                </tr>
                                @forelse ($productGroup->products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        <a href="{{ route('voyager.products.edit', [$product->id]) }}" target="_blank">{{ $product->name }}</a>
                                    </td>
                                    <td>
                                        @foreach ($productGroup->attributes as $productGroupAttribute)
                                            @php
                                                $productAttributeValue = $product->attributeValues->where('attribute_id', $productGroupAttribute->id)->first();
                                            @endphp
                                            <div class="mb-10">
                                                <div>{{ $productGroupAttribute->name }}: {{ $productAttributeValue->name }}</div>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <form action="{{ route('voyager.product_groups.products.detach', [$productGroup->id, $product->id]) }}" method="post">
                                            @csrf
                                            <button type="submit" title="Удалить из группы" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены?')">&times;</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Продукты не добавлены <a href="{{ route('voyager.product_groups.products.create', [$productGroup->id]) }}">Добавить</a>
                                    </td>
                                </tr>
                                @endforelse
                            </table>
                        </div>
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
