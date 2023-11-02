<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $shippingPrice = floatval($this->shipping_price);
        $subtotal = floatval($this->subtotal);
        $total = floatval($this->total);
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'address_id' => $this->address_id,
            'address_line_1' => $this->address_line_1,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_accuracy' => $this->location_accuracy,
            'message' => $this->message,
            'shipping_price' => $shippingPrice,
            'shipping_price_formatted' => Helper::formatPrice($shippingPrice),
            'subtotal' => $subtotal,
            'subtotal_formatted' => Helper::formatPrice($subtotal),
            'total' => $total,
            'total_formatted' => Helper::formatPrice($total),
            // 'communication_method' => $this->communication_method_title,
            'payment_method_id' => $this->payment_method_id,
            'payment_method_title' => $this->payment_method_title,
            'status' => $this->status,
            'status_title' => $this->status_title,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
        return $data;
    }
}
