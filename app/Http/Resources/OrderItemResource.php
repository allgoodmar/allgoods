<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $price = floatval($this->price);
        $total = floatval($this->total);
        $data = [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'quantity' => intval($this->quantity),
            'price' => $price,
            'price_formatted' => Helper::formatPrice($price),
            'total' => $total,
            'total_formatted' => Helper::formatPrice($total),
        ];
        return $data;
    }
}
