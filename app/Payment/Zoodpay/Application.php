<?php

namespace App\Payment\Zoodpay;

use App\Helpers\Helper;
use App\Order;
use App\Services\Zoodpay;
use App\ZoodpayRefund;
use App\ZoodpayTransaction;
use Illuminate\Support\Facades\Log;

class Application
{
    const ACTION_PREPARE = 0;
    const ACTION_COMPLETE = 1;

    public $config;
    public $request;

    /**
     * Application constructor.
     * @param array $config configuration array with <em>merchant_id</em>, <em>login</em>, <em>keyFile</em> keys.
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->request = request();
    }

    /**
     * Authorizes session and handles requests.
     */
    public function run()
    {
        $request = request();

        $requestType = $request->input('zoodpay_operation_type', 'transaction'); // transaction, refund

        switch ($requestType) {
            case 'transaction':
                return $this->handleTransaction();
                break;
            case 'refund':
                return $this->handleRefund();
                break;
            case 'ipn':
                return $this->handleIPN();
                break;
        }
    }

    private function handleTransaction()
    {
        $request = request();
        $zoodpay = new Zoodpay();
        $transaction_id = $request->input('transaction_id', 0);
        $merchant_order_reference = $request->input('merchant_order_reference', 0);
        $order = null;

        // get order
        if ($merchant_order_reference) {
            $orderID = str_replace('order', '', $merchant_order_reference);
            $order = Order::findOrFail($orderID);
        }
        if (!$order) {
            abort(404);
        }

        // check signature
        $signature = $request->input('signature', '');
        if ($signature != $zoodpay->generateZoodpaySignature($order, $transaction_id, true)) {
            abort(403);
        }

        // update transaction
        if ($transaction_id) {
            $zoodpayTransactionStatus = $request->input('status', 'Failed');
            $zoodpayErrorMessage = $request->input('errorMessage', '');
            if (empty($zoodpayErrorMessage) && $zoodpayTransactionStatus == 'Failed') {
                $zoodpayErrorMessage = 'Unknown error';
            }
            ZoodpayTransaction::where('zoodpay_transaction_id', $transaction_id)->update([
                'zoodpay_status' => $zoodpayTransactionStatus,
                'zoodpay_error_message' => $zoodpayErrorMessage,
            ]);
            switch($zoodpayTransactionStatus) {
                case 'Paid':
                    $order->setPaid();

                    // notify telegram group
                    $telegram_chat_id = config('services.telegram.chat_id');
                    Helper::toTelegram('Заказ #' . $order->id . ' оплачен через Zoodpay', 'Markdown', $telegram_chat_id);

                    break;
                case 'Failed':
                    $order->setCancelled();
                    break;
            }
        }
        return redirect()->to($order->url);
    }

    private function handleRefund()
    {
        $request = request();
        $zoodpay = new Zoodpay();
        $refund_id = $request->input('refund_id', 0);
        $refund = $request->input('refund', '');
        $zoodpayRefund = null;


        // get order
        if ($refund_id) {
            $zoodpayRefund = ZoodpayRefund::where('zoodpay_refund_id', $refund_id)->first();
        }

        if (!$zoodpayRefund || empty($refund)) {
            abort(404);
        }

        // check signature
        // $signature = $request->input('signature', '');
        // if ($signature != $zoodpay->generateZoodpaySignature($order, $transaction_id, true)) {
        //     abort(403);
        // }

        $zoodpayTransaction = $zoodpayRefund->zoodpayTransaction;
        $order = $zoodpayTransaction->order;

        // udpate refund
        $zoodpayRefund->zoodpay_status = $refund['status'];
        $zoodpayRefund->zoodpay_declined_reason = $refund['declined_reason'];
        $zoodpayRefund->zoodpay_refund_amount = $refund['refund_amount'];
        if (!empty($refund['refunded_at'])) {
            $zoodpayRefund->zoodpay_refunded_at = $refund['refunded_at'];
        }
        $zoodpayRefund->save();

        // update order
        if ($refund['status'] == 'Processing' || $refund['status'] == 'Done') {
            $order->status = Order::STATUS_REFUND;
            $order->save();
        }

        return redirect()->to($order->url);
    }

    private function handleIPN()
    {
        // $request = request();
        // $zoodpay = new Zoodpay();
        // $refund_id = $request->input('refund_id', 0);
        // $refund = $request->input('refund', '');
        // $zoodpayRefund = null;


        // // get order
        // if ($refund_id) {
        //     $zoodpayRefund = ZoodpayRefund::where('zoodpay_refund_id', $refund_id)->first();
        // }

        // if (!$zoodpayRefund || empty($refund)) {
        //     abort(404);
        // }

        // // check signature
        // // $signature = $request->input('signature', '');
        // // if ($signature != $zoodpay->generateZoodpaySignature($order, $transaction_id, true)) {
        // //     abort(403);
        // // }

        // $zoodpayTransaction = $zoodpayRefund->zoodpayTransaction;
        // $order = $zoodpayTransaction->order;

        // // udpate refund
        // $zoodpayRefund->zoodpay_status = $refund['status'];
        // $zoodpayRefund->zoodpay_declined_reason = $refund['declined_reason'];
        // $zoodpayRefund->zoodpay_refund_amount = $refund['refund_amount'];
        // if (!empty($refund['refunded_at'])) {
        //     $zoodpayRefund->zoodpay_refunded_at = $refund['refunded_at'];
        // }
        // $zoodpayRefund->save();

        // // update order
        // if ($refund['status'] == 'Processing' || $refund['status'] == 'Done') {
        //     $order->status = Order::STATUS_REFUND;
        //     $order->save();
        // }

        // return redirect()->to($order->url);
    }
}
