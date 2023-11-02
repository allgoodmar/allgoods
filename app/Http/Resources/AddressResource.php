<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            // 'name' => $this->name,
            // 'phone_number' => $this->phone_number,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'latitude' => floatval($this->latitude),
            'longitude' => floatval($this->longitude),
            'location_accuracy' => intval($this->location_accuracy),
            'is_default' => $this->is_default,
        ];
        return $data;
    }
}
