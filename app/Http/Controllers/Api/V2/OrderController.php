<?php

namespace App\Http\Controllers\Api\V2;

use App\Address;
use App\Card;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Mail\NewOrderAdminMail;
use App\Mail\NewOrderClientMail;
use App\Order;
use App\Image;
use App\PaymentMethod;
use App\OrderItem;
use App\Services\AtmosService;
use App\Services\AmoCrmService;
use App\Services\GrowCrmService;
use App\Services\TelegramService;
use App\ShippingMethod;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    public function index(Request $request)
    {
        $quantity = (int)$request->input('quantity', 10);
        if ($quantity > 120 || $quantity < 1) {
            $quantity = 10;
        }

        $orders = $request->user()->orders()->latest()->with(['orderItems.product'])->paginate($quantity)->appends($request->all());
        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order)
    {
        $order->load(['user', 'orderItems']);
        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        // get cart
        $cart = app('cart');
        $cartItems = $cart->getContent()->sortBy('id');

        // cart empty error
        if ($cart->isEmpty()) {
            return response()->json([
                'message' => __('main.cart_is_empty'),
            ], 400);
        }

        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|max:191',
            'phone_number' => [
                'required',
                'regex:/' . Helper::phoneNumberRegex() . '/',
            ],
            'email' => 'nullable|email|max:191',
            'address_id' => [
                'required',
                Rule::exists('addresses', 'id')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
            'message' => 'max:50000',
            // 'type' => 'required|integer|in:' . implode(',', array_keys(Order::types())),
            // 'communication_method' => 'required|integer|between:0,2',
            'payment_method_id' => 'required|integer|in:' . implode(',', Helper::paymentMethodsIds()),
            // 'address_line_1' => 'nullable|max:50000',
            // 'latitude' => 'nullable|max:191',
            // 'longitude' => 'nullable|max:191',
            // 'location_accuracy' => 'nullable|max:191',
            // 'installment_payment_months' => 'nullable|max:191',
            // 'total_price_per_month' => 'nullable|numeric',
            'card_id' => 'nullable|exists:cards,id',
        ]);

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

        // check stock
        $lackOfProducts = [];
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->associatedModel;
            $product->refresh();
            if ($product->getStock() < $cartItem->quantity) {
                $lackOfProducts[$product->id] = [
                    'name' => $product->getTranslatedAttribute('name'),
                    'in_stock' => $product->getStock(),
                ];
            }
        }
        if (count($lackOfProducts)) {
            $message = __('main.not_enough_items_in_stock_to_place_an_order') . '.';
            foreach ($lackOfProducts as $key => $value) {
                $message .= ' ' . $value['name'] . ' - ' . __('main.in_stock') . ': ' . $value['in_stock'] . '.';
            }
            return response()->json([
                'message' => $message,
            ], 400);
        }

        // set communication method
        $data['communication_method'] = Order::COMMUNICATION_METHOD_PHONE;

        // set order type
        $data['type'] = Order::TYPE_BUY_IMMEDIATELY;
        if (Helper::isInstallmentPaymentMethod($data['payment_method_id'])) {
            $data['type'] = Order::TYPE_INSTALLMENT;
        }
        // $orderTypes = Order::types();
        // $data['type'] = (int)$data['type'];
        // if (!array_key_exists($data['type'], $orderTypes)) {
        //     $data['type'] = Order::TYPE_BUY_IMMEDIATELY;
        // }

        // set shipping price if order is not installment
        $data['shipping_price'] = 0;
        if ($data['type'] != Order::TYPE_INSTALLMENT) {
            $shippingMethod = ShippingMethod::active()->orderBy('order')->first();
            $data['shipping_method_id'] = $shippingMethod->id ?? null;
            $data['shipping_price'] = $shippingMethod->price ?? 0;
        }

        // admin and test accounts
        if (in_array($user->id, [1, 2, 14, 3366])) {
            $data['shipping_price'] = 0;
        }

        // default status
        $data['status'] = Order::STATUS_PENDING;

        $address = Address::findOrFail($data['address_id']);
        if (empty($data['address_line_1'])) {
            $data['address_line_1'] = $address->address_line_1;
        }
        if (empty($data['latitude'])) {
            $data['latitude'] = $address->latitude;
        }
        if (empty($data['longitude'])) {
            $data['longitude'] = $address->longitude;
        }
        if (empty($data['location_accuracy'])) {
            $data['location_accuracy'] = $address->location_accuracy;
        }
        if (empty($data['message'])) {
            $data['message'] = '';
        }

        $data['user_id'] = auth()->check() ? auth()->user()->id : null;

        // $data['subtotal'] = $cart->getSubTotalWithoutConditions();
        $data['subtotal'] = $cart->getSubTotal();
        $data['total'] = $cart->getTotal() + $data['shipping_price'];

        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->server('HTTP_USER_AGENT');
        $data['source_id'] = Order::SOURCE_API;

        unset($data['public_offer']);

        // check card
        $card = null;
        if (!auth()->check()) {
            $data['card_id'] = null;
        } else {
            if (!empty($data['card_id'])) {
                $card = auth()->user()->cards()->where('cards.id', $data['card_id'])->first();
            }
        }
        if (!$card) {
            $data['card_id'] = null;
        }

        // create order
        $order = Order::create($data);

        if (config('app.env') == 'production') {
            // AMOCRM STORES

            $service = new AmoCrmService();

            $my_order_id = "#".(string)$order->id;

            if (empty($request->message)) {
                $my_message = '';
            }else{
                $my_message = $request->message;
            }

            $p_methods = PaymentMethod::where('id', $request->payment_method_id)->first();

            $my_payment = $p_methods->name;

            // GET IMAGE
            $mainPassportToSend = '';
            $addressPassportToSend = '';
            $newPhone = preg_replace("/[^0-9]/", "", $request->phone_number);
            $checkUser = User::where("phone_number", $newPhone)->first();
            
            
            
            if (!empty($checkUser->passport_main_image)) {
                $checkMainImage = Image::where('id', $checkUser->passport_main_image)->first();
                // $mainPassportToSend = $checkPhone->passport_main_string;
                $mainPassportToSend = "https://allgood.uz" . "/storage/". $checkMainImage->path;
            }else{
                $mainPassportToSend = "https://allgood.uz";
            }

            if (!empty($checkUser->passport_address_image)) {
                $checkAddressImage = Image::where('id', $checkUser->passport_address_image)->first();
                // $addressPassportToSend = $checkPhone->passport_address_string;
                $addressPassportToSend = "https://allgood.uz" . "/storage/". $checkAddressImage->path;
            }else{
                $addressPassportToSend = "https://allgood.uz";
            }
            // END IMAGE

            $single_product = '';
            $single_link = '';
            foreach ($cart->getContent() as $cartItem) {
                $single_product = $cartItem->name;
                $single_link = $cartItem->associatedModel->url;
            }

            $crm_person_name = $request->name . " из приложении";

            $itemsRow = count($cart->getContent());
            if ($itemsRow == 1) {
                $body = [
                    [
                        "name" => $my_order_id,
                        "created_by" => 0,
                        "price" => $data['subtotal'],
                        "_embedded" => [
                            "contacts" => [
                                [
                                "first_name" => $request->name,
                                "created_at" => 1608905348,
                                "responsible_user_id" => 0,
                                "updated_by" => 0,
                                "custom_fields_values" => [
                                        [
                                            "field_id" => 1127431,
                                            "values" => [
                                                [
                                                    "value" => $request->name
                                                ]
                                            ]
                                        ],
                                        [
                                            "field_id" => 815517,
                                            "values" => [
                                                [
                                                    "value" => $request->phone_number
                                                ]
                                            ],
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "custom_fields_values" => [
                            [
                                "field_id" => 840221,
                                "values" => [
                                    [
                                        "value" => $my_order_id
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840225,
                                "values" => [
                                    [
                                        "value" => $crm_person_name
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840265,
                                "values" => [
                                    [
                                        "value" => $request->phone_number
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840267,
                                "values" => [
                                    [
                                        "value" => $address->address_line_1
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840269,
                                "values" => [
                                    [
                                        "value" => $my_message
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840271,
                                "values" => [
                                    [
                                        "value" => $my_payment
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840275,
                                "values" => [
                                    [
                                        "value" => $single_product
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 1120387,
                                "values" => [
                                    [
                                        "value" => $single_link
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 894271,
                                "values" => [
                                    [
                                        "value" => (string)$data['subtotal'] . " so'm"
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 1120385,
                                "values" => [
                                    [
                                        "value" => "⬆️"
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 1152213,
                                "values" => [
                                    [
                                        "value" => $addressPassportToSend
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 1152215,
                                "values" => [
                                    [
                                        "value" => $mainPassportToSend
                                    ]
                                ]
                            ],
                        ],
                    ]
                ];

                $body = json_decode(json_encode($body));
                $service->sendReq($body);
            }else{
                $my_products = [];
                $ii = 1;
                foreach ($cart->getContent() as $cartItem) {
                    $my_products[] = [
                        "value" => "_____" . " (" . $ii++ . "-m)|> Nomi: " . $cartItem->name . " |> Narxi: " . $cartItem->price . " |> Soni: " . $cartItem->quantity . " |> URL: " . $cartItem->associatedModel->url . " <|"
                    ];
                }

                $res_my_products = [];
                foreach ($my_products as $childArray)
                {
                    foreach ($childArray as $value)
                    {
                    $res_my_products[] = $value;
                    }
                }

                $res_my_products = implode("_____", $res_my_products);

                $crm_person_name = $request->name . " из приложении";

                $body = [
                    [
                        "name" => $my_order_id,
                        "created_by" => 0,
                        "price" => $data['subtotal'],
                        "_embedded" => [
                            "contacts" => [
                                [
                                "first_name" => $request->name,
                                "created_at" => 1608905348,
                                "responsible_user_id" => 0,
                                "updated_by" => 0,
                                "custom_fields_values" => [
                                        [
                                            "field_id" => 1127431,
                                            "values" => [
                                                [
                                                    "value" => $request->name
                                                ]
                                            ]
                                        ],
                                        [
                                            "field_id" => 815517,
                                            "values" => [
                                                [
                                                    "value" => $request->phone_number
                                                ]
                                            ],
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "custom_fields_values" => [
                            [
                                "field_id" => 840221,
                                "values" => [
                                    [
                                        "value" => $my_order_id
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840225,
                                "values" => [
                                    [
                                        "value" => $crm_person_name
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840265,
                                "values" => [
                                    [
                                        "value" => $request->phone_number
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840267,
                                "values" => [
                                    [
                                        "value" => $address->address_line_1
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840269,
                                "values" => [
                                    [
                                        "value" => $my_message
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840271,
                                "values" => [
                                    [
                                        "value" => $my_payment
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 840275,
                                "values" => [
                                    [
                                        "value" => "more products below ⬇️"
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 1152213,
                                "values" => [
                                    [
                                        "value" => $addressPassportToSend
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 1152215,
                                "values" => [
                                    [
                                        "value" => $mainPassportToSend
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 894271,
                                "values" => [
                                    [
                                        "value" => (string)$data['subtotal'] . " so'm"
                                    ]
                                ]
                            ],
                            [
                                "field_id" => 1120385,
                                "values" => [
                                    [
                                        "value" => $res_my_products
                                    ]
                                ]
                            ],
                        ],
                    ]
                ];

                $body = json_decode(json_encode($body));
                $service->sendReq($body);
            }

            // AMOCRM ENDS
        }

        foreach ($cart->getContent() as $cartItem) {
            $product = $cartItem->associatedModel;
            $order->orderItems()->create([
                'order_id' => $order->id,
                'name' => $cartItem->name,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'subtotal' => $cartItem->getPriceSum(),
                'total' => $cartItem->getPriceSumWithConditions(),
                'product_id' => $product->id,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'import_partner_id' => $product->import_partner_id,
            ]);
        }

        // create trendyol purchase request
        $trendyolItems = [];
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->associatedModel;
            if ($product->isTrendyolProduct()) {
                $trendyolItems[] = [
                    'barcode' => $product->barcode,
                    'quantity' => $cartItem->quantity,
                ];
            }
        }
        if (count($trendyolItems) > 0) {
            Helper::trendyolPurchase($order, $trendyolItems);
        }

        // online uzcard/humo payment
        $cardPaymentMethod = Helper::paymentMethods()->where('code', 'card')->first();
        if ($cardPaymentMethod && $order->payment_method_id == $cardPaymentMethod->id && $card) {
            Helper::payWithAtmos($order, $card);
        }

        // clear cart
        $cart->clearCartConditions();
        $cart->clear();

        // load relations
        $order->load('orderItems');

        // send notification to telegram admin
        $telegramService = new TelegramService();
        $telegramMessage = view('telegram.admin.new_order', ['url' => route('voyager.orders.show', $order->id), 'order' => $order, 'orderUser' => (auth()->check() ? auth()->user() : null)])->render();
        try {
            // send telegram message
            $chat_id = config('services.telegram.chat_id');
            $telegramService->sendMessage($chat_id, $telegramMessage, 'HTML');
            if ($order->latitude && $order->longitude) {
                $locationParams = [];
                if ($order->location_accuracy) {
                    $locationParams['horizontal_accuracy'] = $order->location_accuracy;
                }
                $telegramService->sendLocation($chat_id, $order->latitude, $order->longitude, $locationParams);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        // send to crm
        // $crm = new GrowCrmService();
        // $leadData = [
        //     'lead_title' => 'Заказ #' . $order->id,
        //     'lead_firstname' => $order->name,
        //     'lead_phone' => $order->phone_number,
        //     'lead_email' => $order->email,
        //     'lead_value' => $order->total,
        //     'lead_description' => view('growcrm.admin.new_order', ['url' => route('voyager.orders.show', $order->id), 'order' => $order])->render(),
        // ];
        // $crm->createLead($leadData);

        try {
            // send email to admin
            Mail::to(setting('contact.email'))->send(new NewOrderAdminMail($order));
        } catch (\Throwable $th) {
            //throw $th;
        }

        try {
            // send email to client
            if ($order->email) {
                Mail::to($order->email)->send(new NewOrderClientMail($order));
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json([
            'message' => __('main.order_accepted'),
        ]);
    }

    public function orderItems(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['orderItems']);
        return OrderItemResource::collection($order->orderItems);
    }
}
