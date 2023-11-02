<?php

namespace App\Http\Controllers;

use App\Order;
use App\Services\Zoodpay;
use App\ZoodpayTransaction;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ZoodpayController extends Controller
{
    public function transactionStore(Request $request)
    {
        // Log::info('transaction store start zoodpay controller');
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);
        $order = Order::findOrFail($data['order_id']);
        $zoodpay = new Zoodpay();

        // check actual transaction exists
        $zoodpayTransaction = ZoodpayTransaction::where('order_id', $order->id)->latest()->first();

        if ($zoodpayTransaction) {
            // get transaction
            $zoodpayTransactionResult = $zoodpay->transactionGet($zoodpayTransaction->zoodpay_transaction_id);
            // Log::info($zoodpayTransactionResult);
            if ($zoodpayTransactionResult['error'] == 0) {
                $zoodpayTransaction->zoodpay_status = $zoodpayTransactionResult['data']->status;
                $zoodpayTransaction->save();

                // redirect user to payment page if transaction is active
                if ($zoodpayTransaction->zoodpay_status == 'In active') {
                    return redirect()->away($zoodpayTransaction->zoodpay_payment_url);
                } else {
                    return redirect()->back()->withError(__('main.zoodpay_create_transaction_error'));
                }

            } else {
                return redirect()->back()->withError(__('main.zoodpay_create_transaction_error'));
            }
        }

        // create new transaction
        $result = $zoodpay->transactionCreate($order);
        // Log::info($result);

        // check result
        if ($result['error'] == 0) {
            // check data
            $resultData = $result['data'];
            if (empty($resultData->session_token) || empty($resultData->transaction_id) || empty($resultData->expiry_time) || empty($resultData->payment_url) || empty($resultData->signature)) {
                return redirect()->back()->withError(__('main.zoodpay_create_transaction_error'));
            }

            // check signature
            if ($resultData->signature != $zoodpay->generateZoodpaySignature($order, $resultData->transaction_id)) {
                abort(403);
            }

            // expiry time
            $expiryTime = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $resultData->expiry_time);
            if (!$expiryTime) {
                abort(403);
            }

            // save zoodpay transaction
            $zoodpayTransaction = ZoodpayTransaction::create([
                'zoodpay_session_token' => $resultData->session_token,
                'zoodpay_transaction_id' => $resultData->transaction_id,
                'zoodpay_expiry_time' => $expiryTime,
                'zoodpay_payment_url' => $resultData->payment_url,
                'zoodpay_signature' => $resultData->signature,
                'order_id' => $order->id,
            ]);

            // redirect user to payment page
            return redirect()->away($resultData->payment_url);
        }

        // return back with error
        return redirect()->back()->withError(__('main.zoodpay_create_transaction_error'));
    }
}
