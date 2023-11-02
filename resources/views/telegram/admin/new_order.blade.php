<b>{{ setting('site.title') }} - Новый заказ</b>
ID: {{ $order->id }}
Источник: {{ $order->source_title }}
Имя: {{ $order->name }}
Телефон: {{ $order->phone_number }}
E-mail: {{ $order->email }}
Адрес: {{ $order->address_line_1 }}
Сообщение: {{ $order->message }}
Способ оплаты: {{ $order->payment_method_title }}
{{-- Тип заказа: {{ $order->type_title }} --}}
Продукты:
@foreach($order->orderItems as $item)
<a href="{{ $item->product->url ?? '' }}">{{ $item->quantity }} x {{ $item->name }}</a> - {{ Helper::formatPrice($item->total) }}
@endforeach
Итого: {{ Helper::formatPrice($order->total) }}
@if (!empty($orderUser))
Рассрочка: {{ $orderUser->installment_data_verified_text }}
@endif

<a href="{{ $url }}">Детали</a>
