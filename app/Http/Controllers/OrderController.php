<?php

namespace App\Http\Controllers;

use App\AlifshopApplication;
use App\AlifshopApplicationItem;
use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Image;
use App\InstallmentOrder;
use App\Mail\NewOrderAdminMail;
use App\Mail\NewOrderClientMail;
use App\Mail\OrderAttemptAdminMail;
use App\User;
use App\Order;
use App\OrderItem;
use App\PartnerInstallment;
use App\Product;
use App\Services\AlifshopAzoService;
use App\Services\AmoCrmService;
use App\Services\GrowCrmService;
use App\Services\IntendService;
use App\Services\TelegramService;
use App\Services\UzumService;
use App\ShippingMethod;
use App\ZoodpayTransaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OrderController extends Controller
{
    public $intendService;
    public $otp;

    public function __construct(Request $request, IntendService $intendService)
    {
        $this->intendService = $intendService;
        if (!empty($request->otp)) {
            $this->otp = $request->otp;
        }
    }

    public function show(Request $request, Order $order, $check)
    {
        if (isset($request->order_id)) {
            $order_intend = Order::find($order->id);
            if ($this->intendService->orderCheck($request->order_id) === true) {
                $order_intend->status = Order::STATUS_PAID;
            } else {
                $order_intend->status = Order::STATUS_CANCELLED;
            }
            $order_intend->save();
            return redirect()->away($order->url);
        }

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.order'), $order->url), LinkItem::STATUS_INACTIVE);

        if ($check != md5($order->created_at)) {
            abort(403);
        }

        $zoodpayTransaction = null;
        if ($order->payment_method_id == Order::PAYMENT_METHOD_ZOODPAY_INSTALLMENTS) {
            $zoodpayTransaction = ZoodpayTransaction::where('order_id', $order->id)->latest()->first();
        }

        return view('order.show', compact('order', 'breadcrumbs', 'zoodpayTransaction'));
    }

    /**
     * Create new order
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function add(Request $request)
    {
        // cart empty error
        if (app('cart')->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $data = $request->validate([
            'name' => 'required|max:191',
            'phone_number' => 'required|regex:/^\+998\d{2}\s\d{3}-\d{2}-\d{2}$/',
            'email' => 'email|max:191',
            'address_line_1' => 'max:50000',
            'message' => 'max:50000',
            // 'type' => 'required|integer|in:' . implode(',', array_keys(Order::types())),
            'communication_method' => 'required|integer|between:0,2',
            'payment_method_id' => 'required|integer|in:' . implode(',', Helper::paymentMethodsIds()),
            'public_offer' => '',
            'latitude' => '',
            'longitude' => '',
            'location_accuracy' => '',
            'alifshop_phone_number' => ['required_if:payment_method_id,' . Order::PAYMENT_METHOD_ALIFSHOP, 'regex:/^\+998\d{2}\s\d{3}-\d{2}-\d{2}$/'],
            'alifshop_otp' => ['nullable', 'required_if:payment_method_id,' . Order::PAYMENT_METHOD_ALIFSHOP, 'regex:/^(\s*|\d{4,6})$/'],
            'installment_payment_months' => 'nullable|max:191',
            'total_price_per_month' => 'nullable|numeric',
            'card_number' => 'nullable',
            'card_validation_date' => 'nullable',
            // 'has_work_permit' => '',
            // 'has_bnpl_already' => '',
        ], [
            'public_offer.required' => __('main.you_must_accept_public_offer'),
        ]);

        // cart
        $cart = app('cart');
        $cartItems = $cart->getContent()->sortBy('id');

        // uzum items
        $uzum_items = [];
        foreach ($cartItems as $key => $value) {
            $uzum_items = [
                'price' => (int) $value->price,
                'amount' => $value->quantity,
                'product_id' => $value->id,
                'name' => $value->name,
                'category' => $value->associatedModel->category_id ?? rand(1, 10),
            ];
        }

        $uzumMonths = null;
        if (!empty($request->uzumMonths)) {
            $uzumMonths = $request->uzumMonths;
        }

        $uzum_contract = null;
        if ($data['payment_method_id'] == Order::PAYMENT_METHOD_UZUM) {
            $processedPhoneNumber = str_replace(['+', ' ', '-'], '', $request->uzum_phone_number);
            $current_url = $request->current_url;
            $uzumService = new UzumService($current_url, $uzum_items, $uzumMonths, $data['installment_payment_months']);
            $checkForUzum = $uzumService->checkStatus($processedPhoneNumber);
            if ($checkForUzum['message'] == "callback_for_status_zero") {
                return redirect()->to($checkForUzum['callback']);
            } elseif ($checkForUzum['message'] == "callback_for_order") {
                Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $checkForUzum['uzum_token']
                ])->get('https://authtest.uzumnasiya.uz', [
                    'contractId' => $checkForUzum['contract_id'],
                    'callback' => $checkForUzum['callback'],
                    'isMocked' => 'true',
                ]);

                $uzum_contract = $checkForUzum['client_act_pdf'];

                return true;
            } 
        }

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
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->associatedModel;
            $product->refresh();
            if ($product->getStock() < $cartItem->quantity) {
                return back()->withInput()->withError(__('main.product_is_out_of_stock'));
            }
        }

        $partnerInstallment = null;
        $partnerInstallmentID = $request->input('partner-installment-id', '');
        if ($partnerInstallmentID) {
            $partnerInstallment = PartnerInstallment::where('id', $partnerInstallmentID)->firstOrFail();
        }

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

        // default status
        $data['status'] = Order::STATUS_PENDING;

        if (empty($data['address_line_1'])) {
            $data['address_line_1'] = '';
        }
        if (empty($data['message'])) {
            $data['message'] = '';
        }

        $data['user_id'] = auth()->check() ? auth()->user()->id : null;

        // $data['subtotal'] = $cart->getSubtotal();
        // $data['total'] = $cart->getTotal() + $data['shipping_price'];

        // make subtotal with coupon
        if (isset(auth()->user()->id)) {
            if (!empty(auth()->user()->coupon_sum)){
                if (auth()->user()->is_coupon_used == 'no'){
                    $data['subtotal'] = $cart->getSubtotal() - auth()->user()->coupon_sum;
                }else{
                    $data['subtotal'] = $cart->getSubtotal() - auth()->user()->coupon_sum;
                }
            }else{
                $data['subtotal'] = $cart->getSubtotal();
            }
        }else{
            $data['subtotal'] = $cart->getSubtotal();
        }
        // make subtotal with coupon ends

        // check total with coupon
        if (isset(auth()->user()->id)) {
            if (!empty(auth()->user()->coupon_sum)){
                if (auth()->user()->is_coupon_used == 'no'){
                    $readyForCalculation = $cart->getTotal() + $data['shipping_price'];
                    $data['total'] = $readyForCalculation - auth()->user()->coupon_sum;
                }else{
                    $readyForCalculation = $cart->getTotal() + $data['shipping_price'];
                    $data['total'] = $readyForCalculation - auth()->user()->coupon_sum;
                }
            }else{
                $data['total'] = $cart->getTotal() + $data['shipping_price'];
            }
        }else{
            $data['total'] = $cart->getTotal() + $data['shipping_price'];
        }
        // check total with coupon ends

        $anorPrice = $data['total'];

        // Order change status coupon
        if (isset(auth()->user()->id)) {
            if (!empty(auth()->user()->coupon_sum)){
                $data['with_coupon'] = 'yes';
                $data['coupon_amount'] = auth()->user()->coupon_sum;
            }
        }

        // reset coupon
        if (isset(auth()->user()->id)) {
            User::where('id', auth()->user()->id)->update([
                'is_coupon_used' => 'yes',
                'coupon_sum' => null
            ]);
        }

        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->server('HTTP_USER_AGENT');
        $data['source_id'] = Order::SOURCE_SITE;

        unset($data['public_offer']);

        // check and create installment alifshop alif nasiya
        if ($data['payment_method_id'] == Order::PAYMENT_METHOD_ALIFSHOP) {
            $alifshopAzoService = new AlifshopAzoService();
            $result = $alifshopAzoService->applicationCreate($data, $cart, $partnerInstallment);
            $alifshopErrors = [];
            if ($result === false) {
                session()->forget('alifshop_otp_sent');
                session()->forget('alifshop_phone_number');
                $alifshopErrors['alifshop_phone_number'] =  __('main.failed_to_send_application');
                return redirect()->back()->withInput()->withErrors($alifshopErrors);
            }
            $alifshopJson = json_decode($result->getBody()->getContents(), true);
            // Log::info($alifshopJson);
            if ($result->getStatusCode() != 200) {
                // dd($alifshopJson);
                if (!empty($alifshopJson['code']) && $alifshopJson['code'] == 'otp_not_sent') {
                    session()->forget('alifshop_otp_sent');
                    session()->forget('alifshop_phone_number');
                    $alifshopErrors['alifshop_phone_number'] = __('main.incorrect_code');
                } elseif (!empty($alifshopJson['code']) && $alifshopJson['code'] == 'wrong_otp') {
                    $alifshopErrors['alifshop_otp'] = __('main.incorrect_code');
                } else {
                    $alifshopErrors['alifshop_otp'] = __('main.incorrect_code');
                }
            }
            if (count($alifshopErrors)) {
                return redirect()->back()->withInput()->withErrors($alifshopErrors);
            }

            if (!empty($alifshopJson['application'])) {
                $alifshopApplication = AlifshopApplication::create([
                    'order_id' => $alifshopJson['application']['order_id'] ?? null,
                    'application_status_id' => $alifshopJson['application']['application_status_id'] ?? null,
                    'application_status_key' => $alifshopJson['application']['application_status_key'] ?? null,
                    'amount' => $alifshopJson['application']['amount'] ?? 0,
                    'down_payment_amount' => $alifshopJson['application']['down_payment_amount'] ?? null,
                    'prepayment' => $alifshopJson['application']['prepayment'] ?? null,
                    'discount' => $alifshopJson['application']['discount'] ?? 0,
                    'duration' => $alifshopJson['application']['duration'] ?? 1,
                ]);
                if ($alifshopApplication) {
                    foreach ($cart->getContent() as $cartItem) {
                        for ($i = 0; $i < $cartItem->quantity; $i++) {
                            AlifshopApplicationItem::create([
                                'alifshop_application_id' => $alifshopApplication->id,
                                'good' => $cartItem->name,
                                'good_type' => $cartItem->associatedModel->category->name ?? config('app.name'),
                                'price' => $cartItem->price,
                                'sku' => $cartItem->associatedModel->unique_code,
                            ]);
                        }
                    }
                }
            }

            // if success clear session
            session()->forget('alifshop_otp_sent');
            session()->forget('alifshop_phone_number');
        }

        // check and create installment anorbank
        if ($data['payment_method_id'] == Order::PAYMENT_METHOD_ANORBANK) {
            if (empty($this->otp)) {
                return $this->anorIndex($anorPrice, $request);
            }else{
                $this->anorIndex($anorPrice, $request);
            }
        }

        // clean data
        unset($data['alifshop_phone_number'], $data['alifshop_otp']);


        $order = Order::create($data);

        // PASSPORT DETAILS BEGIN

        $dataPassport = $request->validate([
            'passport_main_string' => 'nullable|max:5120',
            'passport_address_string' => 'nullable|max:5120',
            'passport_additional_string' => 'nullable|max:5120',
            'plastic_card_string' => 'nullable|max:5120',
        ]);

        $newPhone = preg_replace("/[^0-9]/", "", $request->phone_number);
        if (!empty(auth()->user())) {
            $checkPhone = User::where("phone_number", $newPhone)->first();
            if (!empty($request->passport_main_string)) {
                $createData = [
                    'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_main_string']),
                    'original_name' => $dataPassport['passport_main_string']->getClientOriginalName(),
                    'mime_type' => $dataPassport['passport_main_string']->getClientMimeType(),
                    'size' => $dataPassport['passport_main_string']->getSize(),
                    'user_id' => auth()->user()->id,
                ];

                $image = Image::create($createData);
                User::where('id', auth()->user()->id)->update([
                    'passport_main_image' => $image->id
                ]);
            }

            if (!empty($request->passport_address_string)) {
                $createData = [
                    'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_address_string']),
                    'original_name' => $dataPassport['passport_address_string']->getClientOriginalName(),
                    'mime_type' => $dataPassport['passport_address_string']->getClientMimeType(),
                    'size' => $dataPassport['passport_address_string']->getSize(),
                    'user_id' => auth()->user()->id,
                ];

                $image = Image::create($createData);
                User::where('id', auth()->user()->id)->update([
                    'passport_address_image' => $image->id
                ]);
            }

            if (!empty($request->passport_additional_string)) {
                $createData = [
                    'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_additional_string']),
                    'original_name' => $dataPassport['passport_additional_string']->getClientOriginalName(),
                    'mime_type' => $dataPassport['passport_additional_string']->getClientMimeType(),
                    'size' => $dataPassport['passport_additional_string']->getSize(),
                    'user_id' => auth()->user()->id,
                ];

                $image = Image::create($createData);
                User::where('id', auth()->user()->id)->update([
                    'passport_additional_image' => $image->id
                ]);
            }

            if (!empty($request->plastic_card_string)) {
                $createData = [
                    'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['plastic_card_string']),
                    'original_name' => $dataPassport['plastic_card_string']->getClientOriginalName(),
                    'mime_type' => $dataPassport['plastic_card_string']->getClientMimeType(),
                    'size' => $dataPassport['plastic_card_string']->getSize(),
                    'user_id' => auth()->user()->id,
                ];

                $image = Image::create($createData);
                User::where('id', auth()->user()->id)->update([
                    'plastic_card_image' => $image->id
                ]);
            }
        }else{
            $emailForUser = "u-" . rand() . "@gmail.com";
            $checkPhone = User::where("phone_number", $newPhone)->first();
            if (empty($checkPhone)) {
                $newUser = User::create([
                    'name' => $request->name,
                    'email' => $emailForUser,
                    'avatar' => "user/default.png",
                    'phone_number' => $newPhone,
                    'password' => Hash::make($newPhone),
                ]);

                if (!empty($request->passport_main_string)) {
                    $createData = [
                        'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_main_string']),
                        'original_name' => $dataPassport['passport_main_string']->getClientOriginalName(),
                        'mime_type' => $dataPassport['passport_main_string']->getClientMimeType(),
                        'size' => $dataPassport['passport_main_string']->getSize(),
                        'user_id' => $newUser->id,
                    ];

                    $image = Image::create($createData);
                    User::where('id', $newUser->id)->update([
                        'passport_main_image' => $image->id
                    ]);
                }

                if (!empty($request->passport_address_string)) {
                    $createData = [
                        'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_address_string']),
                        'original_name' => $dataPassport['passport_address_string']->getClientOriginalName(),
                        'mime_type' => $dataPassport['passport_address_string']->getClientMimeType(),
                        'size' => $dataPassport['passport_address_string']->getSize(),
                        'user_id' => $newUser->id,
                    ];

                    $image = Image::create($createData);
                    User::where('id', $newUser->id)->update([
                        'passport_address_image' => $image->id
                    ]);
                }

                if (!empty($request->passport_additional_string)) {
                    $createData = [
                        'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_additional_string']),
                        'original_name' => $dataPassport['passport_additional_string']->getClientOriginalName(),
                        'mime_type' => $dataPassport['passport_additional_string']->getClientMimeType(),
                        'size' => $dataPassport['passport_additional_string']->getSize(),
                        'user_id' => $newUser->id,
                    ];

                    $image = Image::create($createData);
                    User::where('id', $newUser->id)->update([
                        'passport_additional_image' => $image->id
                    ]);
                }

                if (!empty($request->plastic_card_string)) {
                    $createData = [
                        'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['plastic_card_string']),
                        'original_name' => $dataPassport['plastic_card_string']->getClientOriginalName(),
                        'mime_type' => $dataPassport['plastic_card_string']->getClientMimeType(),
                        'size' => $dataPassport['plastic_card_string']->getSize(),
                        'user_id' => $newUser->id,
                    ];

                    $image = Image::create($createData);
                    User::where('id', $newUser->id)->update([
                        'plastic_card_image' => $image->id
                    ]);
                }
            }else{
                if (!empty($request->passport_main_string)) {
                    $createData = [
                        'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_main_string']),
                        'original_name' => $dataPassport['passport_main_string']->getClientOriginalName(),
                        'mime_type' => $dataPassport['passport_main_string']->getClientMimeType(),
                        'size' => $dataPassport['passport_main_string']->getSize(),
                        'user_id' => $checkPhone->id,
                    ];

                    $image = Image::create($createData);
                    User::where('id', $checkPhone->id)->update([
                        'passport_main_image' => $image->id
                    ]);
                }

                if (!empty($request->passport_address_string)) {
                    $createData = [
                        'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_address_string']),
                        'original_name' => $dataPassport['passport_address_string']->getClientOriginalName(),
                        'mime_type' => $dataPassport['passport_address_string']->getClientMimeType(),
                        'size' => $dataPassport['passport_address_string']->getSize(),
                        'user_id' => $checkPhone->id,
                    ];

                    $image = Image::create($createData);
                    User::where('id', $checkPhone->id)->update([
                        'passport_address_image' => $image->id
                    ]);
                }

                if (!empty($request->passport_additional_string)) {
                    $createData = [
                        'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['passport_additional_string']),
                        'original_name' => $dataPassport['passport_additional_string']->getClientOriginalName(),
                        'mime_type' => $dataPassport['passport_additional_string']->getClientMimeType(),
                        'size' => $dataPassport['passport_additional_string']->getSize(),
                        'user_id' => $checkPhone->id,
                    ];

                    $image = Image::create($createData);
                    User::where('id', $checkPhone->id)->update([
                        'passport_additional_image' => $image->id
                    ]);
                }

                if (!empty($request->plastic_card_string)) {
                    $createData = [
                        'path' => Storage::disk('public')->putFile('images/' . date('FY'), $dataPassport['plastic_card_string']),
                        'original_name' => $dataPassport['plastic_card_string']->getClientOriginalName(),
                        'mime_type' => $dataPassport['plastic_card_string']->getClientMimeType(),
                        'size' => $dataPassport['plastic_card_string']->getSize(),
                        'user_id' => $checkPhone->id,
                    ];

                    $image = Image::create($createData);
                    User::where('id', $checkPhone->id)->update([
                        'plastic_card_image' => $image->id
                    ]);
                }
            }
        }

        /*
        $mainPassportToSend = '';
        $addressPassportToSend = '';
        $checkPhone = User::where("phone_number", $newPhone)->first();
        if ($checkPhone->passport_main_string) {
            // $mainPassportToSend = $checkPhone->passport_main_string;
            $mainPassportToSend = "https://allgood.uz" . "/storage/". $checkPhone->passport_main_string;
        }else{
            $mainPassportToSend = "https://allgood.uz";
        }

        if ($checkPhone->passport_address_string) {
            // $addressPassportToSend = $checkPhone->passport_address_string;
            $addressPassportToSend = "https://allgood.uz" . "/storage/". $checkPhone->passport_address_string;
        }else{
            $addressPassportToSend = "https://allgood.uz";
        }*/

        // GET IMAGE
        $mainPassportToSend = '';
        $addressPassportToSend = '';
        $additionalPassportToSend = '';
        $plasticCardToSend = '';

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

        if (!empty($checkUser->passport_additional_image)) {
            $checkAdditionalImage = Image::where('id', $checkUser->passport_additional_image)->first();
            // $addressPassportToSend = $checkPhone->passport_address_string;
            $additionalPassportToSend = "https://allgood.uz" . "/storage/". $checkAdditionalImage->path;
        }else{
            $additionalPassportToSend = "https://allgood.uz";
        }

        if (!empty($checkUser->plastic_card_image)) {
            $checkPlasticCard = Image::where('id', $checkUser->plastic_card_image)->first();
            // $addressPassportToSend = $checkPhone->passport_address_string;
            $plasticCardToSend = "https://allgood.uz" . "/storage/". $checkPlasticCard->path;
        }else{
            $plasticCardToSend = "https://allgood.uz";
        }
        // END IMAGE


        // PASSPORT DETAILS END

        // AMO_CRM_API begins

        $service = new AmoCrmService();

        if (empty($request->address_line_1)) {
            $my_address = '';
        }else{
            $my_address = $request->address_line_1;
        }

        if (empty($request->message)) {
            $my_message = '';
        }else{
            $my_message = $request->message;
        }

        if (empty($request->message)) {
            $my_message = '';
        }else{
            $my_message = $request->message;
        }

        $my_payment = '';
        if ($request->payment_method_id == Order::PAYMENT_METHOD_INTEND) {
            $my_payment = "INTEND";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_ALIFSHOP) {
            $my_payment = "ALIFSHOP";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_CASH) {
            $my_payment = "Наличные";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_CLICK) {
            $my_payment = "CLICK";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_PAYME) {
            $my_payment = "PAYME";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_ALLGOOD) {
            $my_payment = "ALLGOOD";
        }

        if (isset($_COOKIE["utm_source"])) {
            $utm_source = $_COOKIE["utm_source"];
        }else{
            $utm_source = '';
        }

        if (isset($_COOKIE["utm_content"])) {
            $utm_content = $_COOKIE["utm_content"];
        }else{
            $utm_content = '';
        }

        if (isset($_COOKIE["utm_medium"])) {
            $utm_medium = $_COOKIE["utm_medium"];
        }else{
            $utm_medium = '';
        }

        if (isset($_COOKIE["utm_campaign"])) {
            $utm_campaign = $_COOKIE["utm_campaign"];
        }else{
            $utm_campaign = '';
        }

        if (isset($_COOKIE["utm_term"])) {
            $utm_term = $_COOKIE["utm_term"];
        }else{
            $utm_term = '';
        }

        $my_order_id = "#".(string)$order->id;

        $itemsRow = count($cart->getContent());
        if ($itemsRow == 1) {

            $single_product = '';
            $single_link = '';
            foreach ($cart->getContent() as $cartItem) {
                $single_product = $cartItem->name;
                $single_link = $cartItem->associatedModel->url;
            }

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
                                    "value" => $request->name
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
                                    "value" => $my_address
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
                        [
                            "field_id" => 1153283,
                            "values" => [
                                [
                                    "value" => $additionalPassportToSend
                                ]
                            ]
                        ],
                        [
                            "field_id" => 1121021,
                            "values" => [
                                [
                                    "value" => $request->card_number
                                ]
                            ]
                        ],
                        [
                            "field_id" => 1121023,
                            "values" => [
                                [
                                    "value" => $request->card_validation_date
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815525,
                            "values" => [
                                [
                                    "value" => $utm_content
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815527,
                            "values" => [
                                [
                                    "value" => $utm_medium
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815529,
                            "values" => [
                                [
                                    "value" => $utm_campaign
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815531,
                            "values" => [
                                [
                                    "value" => $utm_source
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815533,
                            "values" => [
                                [
                                    "value" => $utm_term
                                ]
                            ]
                        ]
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
                                    "value" => $request->name
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
                                    "value" => $my_address
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
                            "field_id" => 1153283,
                            "values" => [
                                [
                                    "value" => $additionalPassportToSend
                                ]
                            ]
                        ],
                        [
                            "field_id" => 1121021,
                            "values" => [
                                [
                                    "value" => $request->card_number
                                ]
                            ]
                        ],
                        [
                            "field_id" => 1121023,
                            "values" => [
                                [
                                    "value" => $request->card_validation_date
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
                        [
                            "field_id" => 815525,
                            "values" => [
                                [
                                    "value" => $utm_content
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815527,
                            "values" => [
                                [
                                    "value" => $utm_medium
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815529,
                            "values" => [
                                [
                                    "value" => $utm_campaign
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815531,
                            "values" => [
                                [
                                    "value" => $utm_source
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815533,
                            "values" => [
                                [
                                    "value" => $utm_term
                                ]
                            ]
                        ]
                    ],
                ]
            ];

            $body = json_decode(json_encode($body));
            $service->sendReq($body);
        }

        // AMO_CRM_API ends

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.cart'), route('cart.index')));

        $seller_json = [];
        foreach ($cart->getContent() as $cartItem) {
            $product = $cartItem->associatedModel;
            $orderItemData = [
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
            ];
            
            $pr = Product::where('id', $orderItemData['product_id'])->first();

            $seller_json[] = $pr->seller_id;

            $orderItemForSeller = OrderItem::create($orderItemData);
            OrderItem::where('id', $orderItemForSeller->id)->update([
                'seller_id' => $pr->seller_id
            ]);
        }

        if (!empty($seller_json)) {
            $unique_seller_values = array();
            foreach ($seller_json as $value) {
                if (!in_array($value, $unique_seller_values)) {
                    $unique_seller_values[] = $value;
                }
            }

            Order::where('id', $order->id)->update([
                'seller_id' => $unique_seller_values
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

        // clear cart
        app('cart')->clear();

        //  update installment alifshop
        if ($data['payment_method_id'] == Order::PAYMENT_METHOD_ALIFSHOP && $alifshopApplication) {
            $alifshopApplication->order_id = $order->id;
            $alifshopApplication->save();
        }

        // load relations
        $order->load('orderItems');

        // send notification to telegram admin
        $telegramService = new TelegramService();
        $telegramMessage = view('telegram.admin.new_order', ['url' => route('voyager.orders.show', $order->id), 'order' => $order])->render();
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
        $crm = new GrowCrmService();
        $leadData = [
            'lead_title' => 'Заказ #' . $order->id,
            'lead_firstname' => $order->name,
            'lead_phone' => $order->phone_number,
            'lead_email' => $order->email,
            'lead_value' => $order->total,
            'lead_description' => view('growcrm.admin.new_order', ['url' => route('voyager.orders.show', $order->id), 'order' => $order])->render(),
        ];
        $crm->createLead($leadData);

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

        return redirect()->to($order->url)->withSuccess(__('main.order_accepted'))->with("uzum_contract", $uzum_contract);
    }

    public function attempt(Request $request)
    {
        $data = $request->all();
        $cart = app('cart');
        $cartItems = $cart->getContent();

        try {

            // AMO_CRM_API begins

        $service = new AmoCrmService();

        $rand = rand(100000,999999);

        $data['subtotal'] = $cart->getSubtotal();

        if (empty($request->address_line_1)) {
            $my_address = '';
        }else{
            $my_address = $request->address_line_1;
        }

        if (empty($request->message)) {
            $my_message = '';
        }else{
            $my_message = $request->message;
        }

        if (isset($_COOKIE["utm_source"])) {
            $utm_source = $_COOKIE["utm_source"];
        }else{
            $utm_source = '';
        }

        if (isset($_COOKIE["utm_content"])) {
            $utm_content = $_COOKIE["utm_content"];
        }else{
            $utm_content = '';
        }

        if (isset($_COOKIE["utm_medium"])) {
            $utm_medium = $_COOKIE["utm_medium"];
        }else{
            $utm_medium = '';
        }

        if (isset($_COOKIE["utm_campaign"])) {
            $utm_campaign = $_COOKIE["utm_campaign"];
        }else{
            $utm_campaign = '';
        }

        if (isset($_COOKIE["utm_term"])) {
            $utm_term = $_COOKIE["utm_term"];
        }else{
            $utm_term = '';
        }

        $my_payment = '';
        if ($request->payment_method_id == Order::PAYMENT_METHOD_INTEND) {
            $my_payment = "INTEND";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_ALIFSHOP) {
            $my_payment = "ALIFSHOP";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_CASH) {
            $my_payment = "Наличные";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_CLICK) {
            $my_payment = "CLICK";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_PAYME) {
            $my_payment = "PAYME";
        } elseif ($request->payment_method_id == Order::PAYMENT_METHOD_ALLGOOD) {
            $my_payment = "ALLGOOD";
        }

        /*
        // GET IMAGE
        $mainPassportToSend = '';
        $addressPassportToSend = '';
        $additionalPassportToSend = '';

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

        if (!empty($checkUser->passport_additional_image)) {
            $checkAdditionalImage = Image::where('id', $checkUser->passport_additional_image)->first();
            // $addressPassportToSend = $checkPhone->passport_address_string;
            $additionalPassportToSend = "https://allgood.uz" . "/storage/". $checkAdditionalImage->path;
        }else{
            $additionalPassportToSend = "https://allgood.uz";
        }*/
        // END IMAGE

        $itemsRow = count($cart->getContent());
        if ($itemsRow == 1) {

            $single_product = '';
            $single_link = '';
            foreach ($cart->getContent() as $cartItem) {
                $single_product = $cartItem->name;
                $single_link = $cartItem->associatedModel->url;
            }

            $body = [
                [
                    "name" => "Попытка оформления заказа",
                    "created_by" => 0,
                    'status_id' => 47398933,
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
                                    "value" =>(string)$rand
                                ]
                            ]
                        ],
                        [
                            "field_id" => 840225,
                            "values" => [
                                [
                                    "value" => $request->name
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
                                    "value" => $my_address
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
                            "field_id" => 1121021,
                            "values" => [
                                [
                                    "value" => ''
                                ]
                            ]
                        ],
                        [
                            "field_id" => 1121023,
                            "values" => [
                                [
                                    "value" => ''
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815525,
                            "values" => [
                                [
                                    "value" => $utm_content
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815527,
                            "values" => [
                                [
                                    "value" => $utm_medium
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815529,
                            "values" => [
                                [
                                    "value" => $utm_campaign
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815531,
                            "values" => [
                                [
                                    "value" => $utm_source
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815533,
                            "values" => [
                                [
                                    "value" => $utm_term
                                ]
                            ]
                        ]
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

            $body = [
                [
                    "name" => "Попытка оформления заказа",
                    "created_by" => 0,
                    'status_id' => 47398933,
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
                                    "value" => (string)$rand
                                ]
                            ]
                        ],
                        [
                            "field_id" => 840225,
                            "values" => [
                                [
                                    "value" => $request->name
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
                                    "value" => $my_address
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
                            "field_id" => 894271,
                            "values" => [
                                [
                                    "value" => (string)$data['subtotal'] . " so'm"
                                ]
                            ]
                        ],
                        [
                            "field_id" => 1121021,
                            "values" => [
                                [
                                    "value" => ''
                                ]
                            ]
                        ],
                        [
                            "field_id" => 1121023,
                            "values" => [
                                [
                                    "value" => ''
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
                        [
                            "field_id" => 815525,
                            "values" => [
                                [
                                    "value" => $utm_content
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815527,
                            "values" => [
                                [
                                    "value" => $utm_medium
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815529,
                            "values" => [
                                [
                                    "value" => $utm_campaign
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815531,
                            "values" => [
                                [
                                    "value" => $utm_source
                                ]
                            ]
                        ],
                        [
                            "field_id" => 815533,
                            "values" => [
                                [
                                    "value" => $utm_term
                                ]
                            ]
                        ]
                    ],
                ]
            ];

            $body = json_decode(json_encode($body));
            $service->sendReq($body);
        }

        // AMO_CRM_API ends

        } catch (\Throwable $th) {
            //throw $th;
        }

        // send notification to telegram admin
        $telegramService = new TelegramService();
        $telegramMessage = view('telegram.admin.order_attempt', compact('cartItems', 'data'))->render();
        try {
            // send telegram message
            $chat_id = config('services.telegram.chat_id');
            $telegramService->sendMessage($chat_id, $telegramMessage, 'HTML');
        } catch (\Throwable $th) {
            //throw $th;
        }

        // send mail
        try {
            // send email to admin
            Mail::to(setting('contact.email'))->send(new OrderAttemptAdminMail($data, $cartItems));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function print(Request $request, Order $order, $check)
    {
        $locale = app()->getLocale();

        if ($check != md5($order->created_at)) {
            abort(403);
        }

        return view('order.print', compact('order'));
    }

    public function anorIndex($anorPrice, Request $request)
    {
        // GET BEARER ACCESS TOKEN
        $headers = [
            "Content-Type" => "application/x-www-form-urlencoded",
        ];

        $body = [
            "username" => "all_good_marketplace",
            "password" => "309166181",
            "client_id" => "microservice",
            "client_secret" => "ecd859bd-2970-476c-99f7-e984c25e2f27",
            "grant_type" => "password",
            "scope" => "openid"
        ];

        try {

            $dataAnor = Http::asForm()->post('http://172.20.1.1:9080/auth/realms/bill-master/protocol/openid-connect/token', $body);
            $arr = $dataAnor->getBody()->getContents();
            $resultOfBearerToken = $dataAnor->body();
            $resultOfBearerToken = json_decode($resultOfBearerToken);

        } catch (Throwable $e) {
            Log::debug($e);
            return redirect()->back()->with(["askOtp" => "Ошибка соединения.", "successCard" => $request->card_number, "successCardExpiry" => $request->card_validation_date]);
        }

        return $this->checkBilling($anorPrice, $request, $resultOfBearerToken);
    }

    public function checkBilling($anorPrice, Request $request, $resultOfBearerToken)
    {
        // GET BEARER ACCESS TOKEN
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . $resultOfBearerToken->access_token,
        ];
        
        $requested_card_number = str_replace(' ', '', $request->card_number);
        $requested_expiry = str_replace('/', '', $request->card_validation_date);
        $anorPrice = $anorPrice . "00";
        $anorPrice = (int)$anorPrice;
        $anorPrice = $anorPrice * 1.38;
        
        $body = [
            "account" => "allgood",
            "amount" => $anorPrice,
            "recipientId" => "205963037",
            "agentTranId" => uniqid(),
            "currency" => "860",
            "params" => [
                "pan" => $requested_card_number,
                "exp" => $requested_expiry
            ]
        ];


        try {
            if (!empty($this->otp)) {
                $resultOfCheckBilling = '';
            }else{
                $dataAnor = Http::withHeaders($headers)->post('http://172.20.1.2:8886/services/bmms/api/agent/unregistered-check', $body);
                $arr = $dataAnor->getBody()->getContents();
                $resultOfCheckBilling = $dataAnor->body();
                $resultOfCheckBilling = json_decode($resultOfCheckBilling);
                Session::put("checkBillingId", $resultOfCheckBilling->id);
            }
            
        } catch (Throwable $e) {
            Log::debug($e);
            return redirect()->back()->with(["askOtp" => "Ошибка при получении информации о карте.", "successCard" => $request->card_number, "successCardExpiry" => $request->card_validation_date]);
        }

        if (empty($this->otp)) {
            return redirect()->back()->with(["askOtp" => "Вы получили код, введите, пожалуйста.", "askOtpSuccess" => "success", "successCard" => $request->card_number, "successCardExpiry" => $request->card_validation_date]);
        }else{
            return $this->payBilling($request, $resultOfBearerToken, $resultOfCheckBilling);
        }
    }

    public function payBilling(Request $request, $resultOfBearerToken, $resultOfCheckBilling)
    {
        // GET BEARER ACCESS TOKEN
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . $resultOfBearerToken->access_token,
        ];

        $body = [
            "billId" => Session::get("checkBillingId"),
            "type" => "PC",
            "params" => [
                "otp" => $this->otp,
            ]
        ];

        try {
            $dataAnor = Http::withHeaders($headers)->post('http://172.20.1.2:8886/services/bmms/api/agent/unregistered-pay', $body);
            $arr = $dataAnor->getBody()->getContents();
            $resultOfPayBilling = $dataAnor->body();
            $resultOfPayBilling = json_decode($resultOfPayBilling);
            // dd($resultOfPayBilling);
        } catch (Throwable $e) {
            Log::debug($e);
            return redirect()->back()->with(["askOtp" => "Ошибка получения платежа.", "successCard" => $request->card_number, "successCardExpiry" => $request->card_validation_date]);
        }
        // return $this->checkBilling($resultOfPayBilling);
    }
}
