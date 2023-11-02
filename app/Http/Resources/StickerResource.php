<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class StickerResource extends JsonResource
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
            'order' => $this->order,
            'name' => $this->getTranslatedAttribute('name'),
            'img' => $this->image ? $appURL . $this->img : '',
            'micro_img' => $this->image ? $appURL . $this->micro_img : '',
            'small_img' => $this->image ? $appURL . $this->small_img : '',
            'description' => $this->getTranslatedAttribute('description'),
        ];
    }
}
