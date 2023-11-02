<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class BannerResource extends JsonResource
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
            'name' => $this->getTranslatedAttribute('name'),
            'description' => $this->getTranslatedAttribute('description'),
            'button_text' => $this->getTranslatedAttribute('button_text'),
            'url' => $this->getTranslatedAttribute('url'),
            'img' => $this->image ? $appURL . $this->img : '',
            'target' => (empty($this->target_type) || $this->target_type == 'all') ? null : [
                'type' => $this->target_type,
                'id' => $this->targetable_id,
            ],
        ];
    }
}
