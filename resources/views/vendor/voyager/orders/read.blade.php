@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ __('voyager::generic.viewing') }} {{ ucfirst($dataType->getTranslatedAttribute('display_name_singular')) }} &nbsp;

        @can('edit', $dataTypeContent)
            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                {{ __('voyager::generic.edit') }}
            </a>
        @endcan
        @can('delete', $dataTypeContent)
            @if($isSoftDeleted)
                <a href="{{ route('voyager.'.$dataType->slug.'.restore', $dataTypeContent->getKey()) }}" title="{{ __('voyager::generic.restore') }}" class="btn btn-default restore" data-id="{{ $dataTypeContent->getKey() }}" id="restore-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.restore') }}</span>
                </a>
            @else
                <a href="javascript:;" title="{{ __('voyager::generic.delete') }}" class="btn btn-danger delete" data-id="{{ $dataTypeContent->getKey() }}" id="delete-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
                </a>
            @endif
        @endcan

        <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            {{ __('voyager::generic.return_to_list') }}
        </a>
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <!-- form start -->
                    @foreach($dataType->readRows as $row)
                        @php
                        if ($dataTypeContent->{$row->field.'_read'}) {
                            $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_read'};
                        }
                        @endphp
                        <div class="panel-heading" style="border-bottom:0;">
                            <h3 class="panel-title">{{ $row->getTranslatedAttribute('display_name') }}</h3>
                        </div>

                        <div class="panel-body" style="padding-top:0;">
                            @if (isset($row->details->view))
                                @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => 'read', 'view' => 'read', 'options' => $row->details])
                            @elseif($row->type == "image")
                                <img class="img-responsive"
                                     src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                            @elseif($row->type == 'multiple_images')
                                @if(json_decode($dataTypeContent->{$row->field}))
                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                        <img class="img-responsive"
                                             src="{{ filter_var($file, FILTER_VALIDATE_URL) ? $file : Voyager::image($file) }}">
                                    @endforeach
                                @else
                                    <img class="img-responsive"
                                         src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                                @endif
                            @elseif($row->type == 'relationship')
                                 @include('voyager::formfields.relationship', ['view' => 'read', 'options' => $row->details])
                            @elseif($row->type == 'select_dropdown' && property_exists($row->details, 'options') &&
                                    !empty($row->details->options->{$dataTypeContent->{$row->field}})
                            )
                                <?php echo $row->details->options->{$dataTypeContent->{$row->field}};?>
                            @elseif($row->type == 'select_multiple')
                                @if(property_exists($row->details, 'relationship'))

                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                        {{ $item->{$row->field}  }}
                                    @endforeach

                                @elseif(property_exists($row->details, 'options'))
                                    @if (!empty(json_decode($dataTypeContent->{$row->field})))
                                        @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                            @if (@$row->details->options->{$item})
                                                {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                            @endif
                                        @endforeach
                                    @else
                                        {{ __('voyager::generic.none') }}
                                    @endif
                                @endif
                            @elseif($row->type == 'date' || $row->type == 'timestamp')
                                @if ( property_exists($row->details, 'format') && !is_null($dataTypeContent->{$row->field}) )
                                    {{ \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($row->details->format) }}
                                @else
                                    {{ $dataTypeContent->{$row->field} }}
                                @endif
                            @elseif($row->type == 'checkbox')
                                @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                    @if($dataTypeContent->{$row->field})
                                    <span class="label label-info">{{ $row->details->on }}</span>
                                    @else
                                    <span class="label label-primary">{{ $row->details->off }}</span>
                                    @endif
                                @else
                                {{ $dataTypeContent->{$row->field} }}
                                @endif
                            @elseif($row->type == 'color')
                                <span class="badge badge-lg" style="background-color: {{ $dataTypeContent->{$row->field} }}">{{ $dataTypeContent->{$row->field} }}</span>
                            @elseif($row->type == 'coordinates')
                                @include('voyager::partials.coordinates')
                            @elseif($row->type == 'rich_text_box')
                                @include('voyager::multilingual.input-hidden-bread-read')
                                {!! $dataTypeContent->{$row->field} !!}
                            @elseif($row->type == 'file')
                                @if(json_decode($dataTypeContent->{$row->field}))
                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}">
                                            {{ $file->original_name ?: '' }}
                                        </a>
                                        <br/>
                                    @endforeach
                                @else
                                    <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($row->field) ?: '' }}">
                                        {{ __('voyager::generic.download') }}
                                    </a>
                                @endif
                            @else
                                @include('voyager::multilingual.input-hidden-bread-read')
                                <p>{{ $dataTypeContent->{$row->field} }}</p>
                            @endif
                        </div><!-- panel-body -->
                        @if(!$loop->last)
                            <hr style="margin:0;">
                        @endif
                    @endforeach

                </div>

                <div class="panel panel-bordered">
                    @php
                        $dataTypeContent->load('orderItems.product');
                    @endphp
                    <div class="panel-heading">
                        <h3 class="panel-title">Товары</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">

                            <table class="table table-bordered">
                                <tr>
                                    <th>Название</th>
                                    <th>Партнер</th>
                                    <th>SKU</th>
                                    <th>Штрих-код</th>
                                    <th>К-во</th>
                                    <th>Цена</th>
                                    <th></th>
                                </tr>
                                @foreach($dataTypeContent->items as $item)
                                    <tr>
                                        @php
                                            $product = $item->product;
                                        @endphp
                                        <td>
                                            @if($product)
                                                <a href="{{ $product->url }}" target="_blank">{{ $item->name }}</a>
                                            @else
                                                {{ $item->name }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->import_partner_name }}
                                        </td>
                                        <td>
                                            {{ $item->sku }}
                                        </td>
                                        <td>
                                            {{ $item->barcode }}
                                        </td>
                                        <td>
                                            {{ $item->quantity }}
                                        </td>
                                        <td>{{ Helper::formatPrice($item->price) }}</td>
                                        <td>
                                            @if($dataTypeContent->isPending() || $dataTypeContent->isPendingPayment())
                                                @if($item->isTrendyolProduct() && !$dataTypeContent->trendyol_b2b_ids)
                                                    <a href="javascript:;" onclick="confirm('Вы уверены?') && document.getElementById('order-item-cancel-form-{{ $item->id }}').submit()">Отменить</a>
                                                    <form id="order-item-cancel-form-{{ $item->id }}" style="display:none;" action="{{ route('voyager.order-items.cancel', [$item->id]) }}" method="post">
                                                        @csrf
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                </div>

                @if(!$dataTypeContent->delivered_at && $dataTypeContent->status != \App\Order::STATUS_CANCELLED && $dataTypeContent->status != \App\Order::STATUS_CANCELLED_AFTER_PAYMENT)
                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h3 class="panel-title">Добавить дату доставки</h3>
                        </div>
                        <div class="panel-body" style="overflow: visible">
                            <form action="{{ route('voyager.orders.delivery.store', ['order' => $dataTypeContent->id]) }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>Дата доставки</label>
                                    {{-- <input type="text" class="form-control datepicker-date-only" name="delivered_at" value="{{ date('m/d/Y H:i A') }}" data-min-date="{{ date('Y-m-d') }}"> --}}
                                    <input type="text" class="form-control datetimepicker-from-now" name="delivered_at" value="{{ date('Y-m-d H:i') }}" data-min-date="{{ date('Y-m-d') }}">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary m-0">Сохранить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">Статус заказа</h3>
                    </div>
                    <div class="panel-body" style="overflow: visible">

                        <div style="margin-bottom: 20px;">
                            <strong>Текущий статус:</strong>
                            <span>{{ $dataTypeContent->status_title }}</span>
                        </div>

                        @error('status')
                            <div class="text-danger" style="margin-bottom: 20px;"><small>{{ $message }}</small></div>
                        @enderror

                        @foreach (\App\Order::statuses() as $key => $value)
                            <form action="{{ route('voyager.orders.status.update', ['order' => $dataTypeContent->id]) }}" method="post" style="display: inline-block;">

                                @csrf

                                <input type="hidden" name="status" value="{{ $key }}">

                                <div style="margin-bottom: 5px;">
                                    <button class="btn btn-primary m-0 @if($key == $dataTypeContent->status) active @endif" type="submit">{{ $value }}</button>
                                </div>
                            </form>
                        @endforeach

                        {{-- <form action="{{ route('voyager.orders.status.update', ['order' => $dataTypeContent->id]) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label>Статус</label>
                                <select name="status" class="select2 form-control">
                                    @foreach (\App\Order::statuses() as $key => $value)
                                        <option value="{{ $key }}" @if($key == $dataTypeContent->status) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="text-danger"><small>{{ $message }}</small></div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary m-0">Сохранить</button>
                            </div>
                        </form> --}}
                    </div>
                </div>

                @if($dataTypeContent->payment_method_id == \App\Order::PAYMENT_METHOD_ZOODPAY_INSTALLMENTS && $canBeRefundedZoodpay)
                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h3 class="panel-title">Создать возврат ZOODPAY</h3>
                        </div>
                        <div class="panel-body">
                            <form action="{{ route('voyager.orders.refund.store', ['order' => $dataTypeContent->id]) }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>Сумма</label>
                                    <input type="text" class="form-control" name="amount" value="{{ old('amount') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Причина</label>
                                    <textarea class="form-control" name="reason" required>{{ old('reason') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary m-0">Отправить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($zoodpayRefund)
                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h4 class="panel-title">Запрос на возврат создан</h4>
                        </div>
                        <div class="panel-body">
                            ID возврата: {{ $zoodpayRefund->zoodpay_refund_id }} <br>
                            Статус: {{ $zoodpayRefund->zoodpay_status }} <br>
                            @if ($zoodpayRefund->zoodpay_declined_reason)
                                Причина отказа: {{ $zoodpayRefund->zoodpay_declined_reason }}
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="{{ __('voyager::generic.delete_confirm') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    @if ($isModelTranslatable)
        <script>
            $(document).ready(function () {
                $('.side-body').multilingual();
            });
        </script>
    @endif
    <script>
        var deleteFormAction;
        $('.delete').on('click', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) {
                // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');

            $('#delete_modal').modal('show');
        });

    </script>
@stop
