@if($order)

    @php
        $progressBarStatusKeys = [
            \App\Order::STATUS_DELIVERED,
            \App\Order::STATUS_COURIER,
            \App\Order::STATUS_SENT_FOR_DELIVERY,
            // \App\Order::STATUS_ACCEPTED,
            \App\Order::STATUS_PENDING,
        ];
        $dangerStatusKeys = [
            \App\Order::STATUS_CANCELLED_AFTER_PAYMENT,
            \App\Order::STATUS_CANCELLED,
        ];
    @endphp

    @if(in_array($order->status, $progressBarStatusKeys))
        <div class="my-4">
            <div class="inweb-progressbar">
                @foreach ($progressBarStatusKeys as $value)
                    <div class="inweb-progressbar-block @if($order->status == $value) active @endif">
                        <h4>{{ \App\Order::statuses()[$value] }}</h4>
                        @if(!empty(\App\Order::statusDescriptions()[$value]))
                            <p>{{ \App\Order::statusDescriptions()[$value] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="my-4">
            <div class="inweb-progressbar">
                <div class="inweb-progressbar-block @if(in_array($order->status, $dangerStatusKeys)) inweb-progressbar-block-danger @else inweb-progressbar-block-info @endif active">
                    <h4>{{ $order->status_title }}</h4>
                    @if(!empty(\App\Order::statusDescriptions()[$order->status]))
                        <p>{{ \App\Order::statusDescriptions()[$order->status] }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
