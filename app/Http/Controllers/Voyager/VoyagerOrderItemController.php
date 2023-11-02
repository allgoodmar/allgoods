<?php

namespace App\Http\Controllers\Voyager;

use App\OrderItem;
use App\Services\TrendyolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoyagerOrderItemController extends VoyagerBaseController
{
    public function cancel(Request $request, OrderItem $orderItem)
    {
        $order = $orderItem->order;
        $this->authorize('read', $order);
        if (!$order->isPending() && !$order->isPendingPayment()) {
            return $this->redirectWithMessage($order->id, 'Оменить нельзя (ERROR 1)', 'error');
        }
        if ($orderItem->isTrendyolProduct()) {
            if (empty($order->trendyol_request_number) || !empty($order->trendyol_b2b_ids)) {
                return $this->redirectWithMessage($order->id, 'Оменить нельзя (ERROR 2)', 'error');
            }
            $trendyol = new TrendyolService();
            $res = $trendyol->cancel($order->trendyol_request_number, [
                'barcode' => $orderItem->barcode,
                'quantity' => $orderItem->quantity,
            ]);
            if ($res && ($res->getStatusCode() == 200)) {
                $order->subtotal -= $orderItem->subtotal;
                $order->total -= $orderItem->total;
                $order->save();
                $orderItem->delete();
                return $this->redirectWithMessage($order->id, 'Товар удален из заказа', 'success');
            } else {
                return $this->redirectWithMessage($order->id, 'Оменить нельзя (ERROR 3)', 'error');
            }

        }
        return $this->redirectWithMessage($order->id, 'Заказ не был изменен', 'info');
    }

    private function redirectWithMessage($orderID, $message, $type)
    {
        return redirect()->route('voyager.orders.show', ['id' => $orderID])->with([
            'message'    => $message,
            'alert-type' => $type,
        ]);
    }
}
