<?php

namespace App\Http\Controllers;

use App\Address;
use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Image;
use App\Order;
use App\Page;
use App\Partner;
use App\PartnerInstallment;
use App\Product;
use App\Services\IntendService;
use App\ShippingMethod;
use Darryldecode\Cart\CartCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public $intendService;

    public function __construct(IntendService $intendService)
    {
        $this->intendService = $intendService;
    }

    public function index()
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.cart'), route('cart.index'), LinkItem::STATUS_INACTIVE));
        $cart = app('cart');
        $cartItems = $cart->getContent()->sortBy('id');
        $standardPriceTotal = 0;
        $checkoutAvailable = true;

        // update trendyol stock
        $trendyolProductsBarcodes = [];
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->associatedModel;
            if ($product->isTrendyolProduct()) {
                $trendyolProductsBarcodes[] = $product->barcode;
            }
        }
        if (count($trendyolProductsBarcodes)) {
            Helper::trendyolUpdateStock($trendyolProductsBarcodes);
        }

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->associatedModel;
            $product->refresh();
            $standardPriceTotal += $product->price * $cartItem->quantity;
            $stock = $product->getStock();
            $cartItem->availableQuantity = $stock;
            if ($stock < $cartItem->quantity) {
                $checkoutAvailable = false;
            }
        }
        $discount = $standardPriceTotal - $cart->getTotal();

        $addresses = collect();
        $address = null;
        if (auth()->check()) {
            $user = auth()->user();
            $addresses = $user->addresses;
            $address = $user->addresses()->where('status', Address::STATUS_ACTIVE)->latest()->first();
            if (!$address) {
                $address = $user->addresses()->latest()->first();
                if ($address) {
                    $address->update(['status' => Address::STATUS_ACTIVE]);
                }
            }
        }

        return view('cart', compact('breadcrumbs', 'cart', 'cartItems', 'checkoutAvailable', 'standardPriceTotal', 'discount', 'address', 'addresses'));
    }

    public function checkout(Request $request)
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.cart'), route('cart.index')));
        $breadcrumbs->addItem(new LinkItem(__('main.checkout'), route('cart.checkout'), LinkItem::STATUS_INACTIVE));

        $cart = app('cart');
        $shippingMethods = ShippingMethod::active()->orderBy('order')->get();

        // partner installments
        $partnerInstallmentID = $request->input('partner-installment-id', '');
        $partnerInstallment = null;
        $partnerID = null;
        if ($partnerInstallmentID) {
            $partnerInstallment = PartnerInstallment::where('id', $partnerInstallmentID)->firstOrFail();
            $partnerID = $partnerInstallment->partner_id;
        }

        $partner_details_id = '';
        $partner_details_name = '';
        if ($partnerInstallmentID) {
            $partner_details = Partner::where('id', $partnerID)->firstOrFail();
            $partner_details_id = $partner_details->id;
            $partner_details_name = $partner_details->name;
        }

        // set payment methods
        $paymentMethods = Helper::paymentMethodsDesktop();
        $paymentMethods = $paymentMethods->filter(function($value) use ($partnerID) {
            switch ($partnerID) {
                case 1:
                    return in_array($value->id, [
                        Order::PAYMENT_METHOD_INTEND,
                    ]);
                case 2:
                    return in_array($value->id, [
                        Order::PAYMENT_METHOD_ALIFSHOP,
                    ]);
                case 5:
                    return in_array($value->id, [
                        Order::PAYMENT_METHOD_ALIFSHOP,
                    ]);
                default:
                    return in_array($value->id, [
                        Order::PAYMENT_METHOD_CASH,
                        Order::PAYMENT_METHOD_CLICK,
                        Order::PAYMENT_METHOD_PAYME,
                    ]);
            }
        });

        // if there is product id in request delete other products from cart and set quantity 1 (installment one product)
        $productID = $request->input('product-id', '');
        if ($productID) {
            foreach ($cart->getContent() as $cartItem) {
                if ($cartItem->id != $productID) {
                    $cart->remove($cartItem->id);
                }
            }
            $cart->update($productID, [
                'quantity' => [
                    'relative' => false,
                    'value' => 1,
                ],
            ]);
        }

        // cart items
        $cartItems = $cart->getContent()->sortBy('id');
        $cartItemsPrices = [];

        // set shipping price it is not installment
        $shippingPrice = 0;
        if (!$partnerInstallmentID) {
            $shippingPrice = $shippingMethods->first()->price ?? 0;
        }

        // total price and cart items price
        $totalPrice = $cart->getTotal() + $shippingPrice;
        $totalPricePerMonth = 0;
        $partnerInstallmentDuration = 0;
        if ($partnerInstallmentID) {
            $totalPricePerMonth = Helper::partnerInstallmentPricePerMonth($partnerInstallmentID, $totalPrice);
            $partnerInstallmentDuration = Helper::partnerInstallmentDuration($partnerInstallmentID);
            foreach ($cartItems as $cartItem) {
                $cartItemsPrices[$cartItem->id] = Helper::partnerInstallmentPricePerMonth($partnerInstallmentID, $cartItem->getPriceSumWithConditions());
            }
        } else {
            foreach ($cartItems as $cartItem) {
                $cartItemsPrices[$cartItem->id] = $cartItem->getPriceSumWithConditions();
            }
        }

        $orderAddUrl = $partnerInstallmentID ? route('order.add', ['partner-installment-id' => $partnerInstallmentID]) : route('order.add');

        // $intend_price = $this->intendService->checkCalculateIntend($cartItems);

        $publicOfferPage = Page::find(12);

        // update trendyol stock
        $trendyolProductsBarcodes = [];
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->associatedModel;
            if ($product->isTrendyolProduct()) {
                $trendyolProductsBarcodes[] = $product->barcode;
            }
        }
        if (count($trendyolProductsBarcodes)) {
            Helper::trendyolUpdateStock($trendyolProductsBarcodes);
        }

        $checkoutAvailable = true;
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->associatedModel;
            $product->refresh();
            $stock = $product->getStock();
            $cartItem->availableQuantity = $stock;
            if ($stock < $cartItem->quantity) {
                $checkoutAvailable = false;
            }
        }

        $address = null;
        if (auth()->check()) {
            $user = auth()->user();
            $address = $user->addresses()->where('status', Address::STATUS_ACTIVE)->latest()->first();
            if (!$address) {
                $address = $user->addresses()->latest()->first();
                if ($address) {
                    $address->update(['status' => Address::STATUS_ACTIVE]);
                }
            }
        }

        $orderTypes = Order::types();
        $communicationMethods = Order::communicationMethods();

        $partnersPrices = Helper::partnersPrices($product->current_price);

        if(!empty(auth()->user())){
            $myUser = auth()->user();
            $checkMainImage = Image::where('id', $myUser->passport_main_image)->first();
            $checkAddressImage = Image::where('id', $myUser->passport_address_image)->first();
            $checkAdditionalImage = Image::where('id', $myUser->passport_additional_image)->first();
            $checkPlasticCardImage = Image::where('id', $myUser->plastic_card_image)->first();
        }

        if (empty($checkMainImage)) {
            $checkMainImage = '';
        }
        if (empty($checkAddressImage)) {
            $checkAddressImage = '';
        }
        if (empty($checkAdditionalImage)) {
            $checkAdditionalImage = '';
        }
        if (empty($checkPlasticCardImage)) {
            $checkPlasticCardImage = '';
        }

        return view('checkout', compact('checkMainImage', 'checkAddressImage', 'checkAdditionalImage', 'checkPlasticCardImage', 'partner_details_name', 'product', 'orderAddUrl', 'totalPrice', 'totalPricePerMonth', 'partnerInstallmentDuration', 'partnersPrices', 'breadcrumbs', 'shippingMethods', 'shippingPrice', 'cart', 'cartItems', 'cartItemsPrices', 'checkoutAvailable', 'publicOfferPage', 'orderTypes', 'communicationMethods', 'paymentMethods', 'address'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer'
        ]);

        $data['associatedModel'] = Product::findOrFail($data['id']);
        if (
            $data['associatedModel']->current_price != $data['price']
            // || trim($data['associatedModel']->name) != trim($data['name'])
        ) {
            abort(400);
        }

        // update stock
        $product = Product::findOrFail($data['id']);
        if ($product->isTrendyolProduct()) {
            Helper::trendyolUpdateStock([$product->barcode]);
            $product->refresh();
        }

        // check stock
        if ($product->getStock() < 1) {
            return response([
                'message' => __('main.product_is_out_of_stock'),
            ], 422);
        }

        // Log::info($data);

        app('cart')->add($data);

        return response([
            'cart' => $this->getCartInfo(app('cart')),
            'message' => __('main.product_added_to_cart'),
        ], 201);
    }

    public function debug(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer'
        ]);
        $data['associatedModel'] = Product::findOrFail($request->input('id'));

        $cart = app('cart')->add($data);
        $cart = $cart->getContent()->toArray();
        dd($cart);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:products,id',
            'quantity' => 'required|integer'
        ]);

        app('cart')->update($data['id'], [
            'quantity' => [
                'relative' => false,
                'value' => $data['quantity'],
            ],
        ]);

        $item = app('cart')->get($data['id']);

        $lineTotal = $item->getPriceSum();

        return response([
            'cart' => $this->getCartInfo(app('cart')),
            'lineTotal' => $lineTotal,
            'lineTotalFormatted' => Helper::formatPrice($lineTotal),
            'message' => __('main.cart_updated')
        ], 200);
    }

    public function delete($id)
    {
        app('cart')->remove($id);

        return response(array(
            'cart' => $this->getCartInfo(app('cart')),
            'message' => __('main.product_removed_from_cart')
        ), 200);
    }

    public function addCondition()
    {
        $v = validator(request()->all(), [
            'name' => 'required|string',
            'type' => 'required|string',
            'target' => 'required|string',
            'value' => 'required|string',
        ]);

        if ($v->fails()) {
            return response(array(
                'success' => false,
                'data' => [],
                'message' => $v->errors()->first()
            ), 400, []);
        }

        $name = request('name');
        $type = request('type');
        $target = request('target');
        $value = request('value');

        $cartCondition = new CartCondition([
            'name' => $name,
            'type' => $type,
            'target' => $target, // this condition will be applied to cart's subtotal when getSubTotal() is called.
            'value' => $value,
            'attributes' => array()
        ]);

        app('cart')->condition($cartCondition);

        return response(array(
            'success' => true,
            'data' => $cartCondition,
            'message' => "condition added."
        ), 201, []);
    }

    public function clearCartConditions()
    {
        app('cart')->clearCartConditions();

        return response(array(
            'success' => true,
            'data' => [],
            'message' => "cart conditions cleared."
        ), 200, []);
    }

    private function getCartInfo($cart)
    {
        $subtotal = $cart->getSubTotalWithoutConditions();
        $total = $cart->getTotal();

        $standardPriceTotal = 0;
        $cartItems = $cart->getContent()->sortBy('id');
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->associatedModel;
            $standardPriceTotal += $product->old_price * $cartItem->quantity;
        }

        $discount = $standardPriceTotal - $total;

        return [
            'quantity' => $cart->getTotalQuantity(),
            'subtotal' => $subtotal,
            'subtotalFormatted' => Helper::formatPrice($subtotal),
            'total' => $total,
            'totalFormatted' => Helper::formatPrice($total),
            'standardPriceTotal' => $standardPriceTotal,
            'standardPriceTotalFormatted' => Helper::formatPrice($standardPriceTotal),
            'discount' => $discount,
            'discountFormatted' => Helper::formatPrice($discount),
        ];
    }
}
