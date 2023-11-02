<?php

namespace App\Http\Controllers;

use App\AtmosTransaction;
use App\Helpers\Helper;
use App\Order;
use App\Payment\Click\Application as ClickApplication;
use App\Payment\Click\ClickException;
use App\Payment\Paycom\Application as PaycomApplication;
use App\Payment\Paycom\PaycomException;
use App\Payment\Zoodpay\Application as ZoodpayApplication;
use App\Payment\Zoodpay\ZoodpayException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentGatewayController extends Controller
{
    public function paycom(Request $request)
    {
        // Log::info('in payme controller');
        try {
            $paycomConfig = config('services.paycom');
            $application = new PaycomApplication($paycomConfig);
            $application->run();
        } catch (PaycomException $exc) {
            $exc->send();
        }
    }

    public function click(Request $request)
    {
        // Log::info('in click controller');
        try {
            $clickConfig = config('services.click');
            $application = new ClickApplication($clickConfig);
            $application->run();
        } catch (ClickException $e) {
            $e->send();
        }
    }

    public function zoodpay(Request $request)
    {
        // Log::info('in zoodpay controller');
        try {
            $config = config('services.zoodpay');
            $application = new ZoodpayApplication($config);
            return $application->run();
        } catch (ZoodpayException $e) {
            $e->send();
        }
    }

    public function atmos(Request $request)
    {
        if (!in_array($request->ip(), ['185.8.212.47', '185.8.212.48'])) {
            return response()->json([
                'status' => 0,
                'message' => 'Доступ запрещен',
            ]);
        }

        $storeId = $request->input('store_id', '');
        $transactionId = $request->input('transaction_id', '');
        $transactionTime = $request->input('transaction_time', '');
        $amount = floatval($request->input('amount', -1));
        $invoice = $request->input('invoice', '');
        $sign = $request->input('sign', '');

        if (empty($storeId) || $storeId != config('services.atmos.store_id') || empty($transactionId) || empty($transactionTime) || empty($amount) || empty($invoice) || empty($sign)) {
            return response()->json([
                'status' => 0,
                'message' => 'Не полные данные',
            ]);
        }

        // check sign
        $generatedSign = md5($storeId . $transactionId . $invoice . $amount . config('services.atmos.api_key'));
        if ($generatedSign != $sign) {
            return response()->json([
                'status' => 0,
                'message' => 'Доступ запрещен',
            ]);
        }

        // check order details
        $order = null;
        if (Str::startsWith($invoice, 'order-id-')) {
            $orderId = Str::replace('order-id-', '', $invoice);
            $order = Order::find($orderId);
        }
        if (!$order) {
            return response()->json([
                'status' => 0,
                'message' => 'Заказ не найден',
            ]);
        }
        if (!$order->isPending()) {
            return response()->json([
                'status' => 0,
                'message' => 'Заказ уже обработан',
            ]);
        }
        if ($order->total_tiyin != $amount) {
            return response()->json([
                'status' => 0,
                'message' => 'Неправильная сумма заказа',
            ]);
        }

        // success - notify telegram group
        $telegram_chat_id = config('services.telegram.chat_id');
        Helper::toTelegram('Заказ #' . $order->id . ' оплачен через Atmos', 'Markdown', $telegram_chat_id);

        return response()->json([
            'status' => 1,
            'message' => 'Успешно'
        ]);
    }
}
