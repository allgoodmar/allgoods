<b>{{ setting('site.title') }} - Попытка оформления заказа</b>
Имя: {{ $data['name'] ?? '' }}
Телефон: {{ $data['phone_number'] ?? '' }}
Адрес: {{ $data['address'] ?? '' }}
Сообщение: {{ $data['message'] ?? '' }}
Способ оплаты: {{ Helper::paymentMethodTitle($data['payment_method_id'] ?? '') }}
@if (!empty($data['alifshop_phone_number'])) Телефон alif nasiya: {{ $data['alifshop_phone_number'] ?? '' }} @endif

Продукты:
@foreach($cartItems as $item)
<a href="{{ $item->associatedModel->url }}">{{ $item->quantity }} x {{ $item->name }}</a> - {{ Helper::formatPrice($item->getPriceSumWithConditions()) }}
@endforeach
