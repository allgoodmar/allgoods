<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class StaticTextResource extends JsonResource
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
            'key' => $this->key,
            'name' => $this->getTranslatedAttribute('name'),
            'description' => $this->getTranslatedAttribute('description'),
            'img' => $this->image ? $appURL . $this->img : '',
        ];
    }
}
