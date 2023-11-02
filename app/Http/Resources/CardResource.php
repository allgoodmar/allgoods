<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class CardResource extends JsonResource
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
            // 'atmos_card_id' => $this->atmos_card_id,
            // 'atmos_card_token' => $this->atmos_card_token,
            'pan' => $this->pan,
            'expiry' => $this->expiry,
            'card_holder' => $this->card_holder,
            'is_default' => $this->is_default,
        ];
        return $data;
    }
}
