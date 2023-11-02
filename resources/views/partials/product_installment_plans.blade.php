<div class="product-page-box product-page-installment-payment-box">
    <h5 class="small-header mb-2">{{ __('main.installment_payment') }}</h5>
    <div class="table-responsive">
        <table class="table table-bordered installment-payment-table" data-product-id="{{ $product->id }}">
            <tr>
                <th>{{ __('main.price') }}</th>
                <th>{{ __('main.months') }}</th>
            </tr>
            @foreach ($product->installmentPlans as $installmentPlan)
                <tr>
                    <td>{{ __('main.price_per_month', ['price' => Helper::formatPrice($product->installment_price / $installmentPlan->months)]) }}</td>
                    <td>x {{ $installmentPlan->months }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
