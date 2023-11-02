<p><b>{{ setting('site.title') }} - Новый заказ</b></p>
<p>ID: {{ $order->id }}</p>
<p>Источник: {{ $order->source_title }}</p>
<p>Имя: {{ $order->name }}</p>
<p>Телефон: {{ $order->phone_number }}</p>
<p>E-mail: {{ $order->email }}</p>
<p>Адрес: {{ $order->address_line_1 }}</p>
<p>Сообщение: {{ $order->message }}</p>
<p>Метод коммуникации: {{ $order->communication_method_title }}</p>
<p>Способ оплаты: {{ $order->payment_method_title }}</p>
<p>Тип заказа: {{ $order->type_title }}</p>
@if($order->type == \App\Order::TYPE_INSTALLMENT)
<p>Телефон (для карты): {{ $order->card_phone_number }}</p>
<p>Номер карты: {{ $order->card_number }}</p>
<p>Срок действия: {{ $order->card_expiry_date }}</p>
@endif
<p>Продукты:</p>
@foreach($order->orderItems as $item)
<p><a href="{{ $item->product->url ?? '' }}" target="__blank">{{ $item->quantity }} x {{ $item->name }}</a> - {{ Helper::formatPrice($item->total) }}</p>
@endforeach
<p>Итого: {{ Helper::formatPrice($order->total) }}</p>
<p><a href="{{ $url }}" target="__blank">Детали</a></p>
