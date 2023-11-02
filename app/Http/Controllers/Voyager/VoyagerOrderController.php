<?php

namespace App\Http\Controllers\Voyager;

use App\Attribute;
use App\AttributeValue;
use App\Helpers\Helper;
use App\Order;
use App\Product;
use App\Services\TrendyolService;
use App\Services\Zoodpay;
use App\ZoodpayRefund;
use App\ZoodpayTransaction;
use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use const JSON_UNESCAPED_UNICODE;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;

class VoyagerOrderController extends VoyagerBaseController
{
    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $isSoftDeleted = false;

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
            if ($dataTypeContent->deleted_at) {
                $isSoftDeleted = true;
            }
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'read', $isModelTranslatable);

        // trendyol update b2b ids
        if ($dataTypeContent->trendyol_request_number) {
            $trendyol = new TrendyolService();
            $res = $trendyol->b2bs($dataTypeContent->trendyol_request_number);
            if ($res && ($res->getStatusCode() == 200)) {
                $json = json_decode($res->getBody()->getContents());
                if ($json && is_array($json)) {
                    $b2bids = [];
                    foreach ($json as $row) {
                        $b2bids[] = $row->b2BId;
                    }
                    $dataTypeContent->trendyol_b2b_ids = implode(',', $b2bids);
                    $dataTypeContent->save();
                }
            }
        }

        // zoodpay
        $zoodpayRefund = null;
        $canBeRefundedZoodpay = false;
        $zoodpay = new Zoodpay();
        $zoodpayTransaction = ZoodpayTransaction::where('order_id', $dataTypeContent->id)->where('zoodpay_status', 'Paid')->latest()->first();
        if ($zoodpayTransaction) {
            // check refunds created before
            $zoodpayRefund = $zoodpayTransaction->zoodpayRefunds()->whereNotNull('zoodpay_refund_id')->first();
            if (!$zoodpayRefund) {
                $canBeRefundedZoodpay = true;
            } else {
                // update refund
                $zoodpayRefundResult = $zoodpay->refundGet($zoodpayRefund->zoodpay_refund_id);
                // Log::info($zoodpayRefundResult);
                if ($zoodpayRefundResult['error'] == 0) {
                    $zoodpayRefund->zoodpay_status = $zoodpayRefundResult['data']->refund->status;
                    $zoodpayRefund->zoodpay_declined_reason = $zoodpayRefundResult['data']->refund->declined_reason;
                    $zoodpayRefund->zoodpay_refund_amount = $zoodpayRefundResult['data']->refund->refund_amount;
                    $zoodpayRefund->save();
                }
            }
        }

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted', 'canBeRefundedZoodpay', 'zoodpayRefund'));
    }

    public function deliveryStore(Request $request, Order $order)
    {
        // Check permission
        $this->authorize('read', $order);

        $data = $request->validate([
            'delivered_at' => 'required',
        ], [
            '*.required' => 'Обязательное поле',
        ]);

        $date = Carbon::createFromFormat('Y-m-d H:i', $data['delivered_at']);
        if (!$date) {
            abort(403);
        }

        // udpate order
        $order->update([
            'delivered_at' => $date,
        ]);

        // update zoodpay order
        if ($order->payment_method_id == Order::PAYMENT_METHOD_ZOODPAY_INSTALLMENTS) {
            $zoodpayTransaction = ZoodpayTransaction::where('order_id', $order->id)->orderBy('id', 'desc')->first();
            if ($zoodpayTransaction) {
                $zoodpay = new Zoodpay();
                $zoodpay->setDeliveryDate($zoodpayTransaction->zoodpay_transaction_id, $date);
            }
        }

        return redirect()->route('voyager.orders.show', ['id' => $order->id])->with([
            'message'    => 'Дата доставки сохранена',
            'alert-type' => 'success',
        ]);
    }

    public function refundStore(Request $request, Order $order)
    {
        // Check permission
        $this->authorize('read', $order);

        // Log::info('transaction store start zoodpay controller');
        $data = $request->validate([
            'amount' => 'required|numeric',
            'reason' => 'required|max:5000',
        ]);

        $zoodpayTransaction = $order->zoodpayTransactions()->where('zoodpay_status', 'Paid')->first();
        if (!$zoodpayTransaction) {
            return $this->redirectWithMessage($order->id, __('main.cannot_create_refund') . ' 1', 'error');
        }

        $zoodpay = new Zoodpay();

        // update transaction
        $zoodpayTransactionResult = $zoodpay->transactionGet($zoodpayTransaction->zoodpay_transaction_id);
        // Log::info('Transaction get ' . print_r($zoodpayTransactionResult, 1));
        if ($zoodpayTransactionResult['error'] != 0) {
            return $this->redirectWithMessage($order->id, __('main.cannot_create_refund') . ' 2', 'error');
        }

        $zoodpayTransaction->zoodpay_status = $zoodpayTransactionResult['data']->status;
        $zoodpayTransaction->save();

        if ($zoodpayTransaction->zoodpay_status != 'Paid') {
            return $this->redirectWithMessage($order->id, __('main.cannot_create_refund') . ' 3', 'error');
        }

        // create refund
        $zoodpayRefundResult = $zoodpay->refundCreate($zoodpayTransaction, $data['amount'], $data['reason']);
        // Log::info('Refund get ' . print_r($zoodpayRefundResult, 1));

        // check result
        if ($zoodpayRefundResult['error'] != 0) {
            return $this->redirectWithMessage($order->id, __('main.cannot_create_refund') . ' 4', 'error');
        }

        // check data
        $resultData = $zoodpayRefundResult['data'];

        if (empty($resultData->refund_id) || empty($resultData->refund)) {
            return $this->redirectWithMessage($order->id, __('main.cannot_create_refund') . ' 5', 'error');
        }

        // udpate order
        $order->status = Order::STATUS_REFUND_REQUESTED;
        $order->save();

        // update refund
        $zoodpayRefund = ZoodpayRefund::where('zoodpay_request_id', $resultData->refund->request_id)->first();
        if (!$zoodpayRefundResult) {
            return $this->redirectWithMessage($order->id, __('main.cannot_create_refund') . ' 6', 'error');
        }
        $zoodpayRefund->update([
            'zoodpay_refund_id' => $resultData->refund->refund_id,
            'zoodpay_status' => $resultData->refund->status,
            'zoodpay_refund_amount' => $resultData->refund->refund_amount,
            'zoodpay_declined_reason' => $resultData->refund->declined_reason,
            'zoodpay_created_at' => $resultData->refund->created_at,
        ]);

        // redirect user to payment page
        return $this->redirectWithMessage($order->id, __('main.refund_request_created'), 'success');
    }

    public function statusUpdate(Request $request, Order $order)
    {
        // Check permission
        $this->authorize('read', $order);

        $statusKeys = array_map(function($item){
            return (string)$item;
        }, array_keys(Order::statuses()));

        $data = $request->validate([
            'status' => 'required|in:' . implode(',', $statusKeys),
        ], [
            '*.required' => 'Обязательное поле',
        ]);

        // udpate order
        $order->update([
            'status' => $data['status'],
        ]);

        return redirect()->route('voyager.orders.show', ['id' => $order->id])->with([
            'message'    => 'Статус сохранен',
            'alert-type' => 'success',
        ]);
    }

    private function redirectWithMessage($orderID, $message, $type)
    {
        return redirect()->route('voyager.orders.show', ['id' => $orderID])->with([
            'message'    => $message,
            'alert-type' => $type,
        ]);
    }
}
