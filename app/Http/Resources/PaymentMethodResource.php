<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $appURL = config('app.url');
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->getTranslatedAttribute('name'),
            'img' => $this->image ? $appURL . $this->img : '',
            'micro_img' => $this->image ? $appURL . $this->micro_img : '',
            'description' => $this->getTranslatedAttribute('description'),
            'installment' => (bool)$this->installment,
        ];
    }
}
