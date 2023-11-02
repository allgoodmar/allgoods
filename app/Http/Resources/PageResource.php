<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class PageResource extends JsonResource
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
            'url' => $this->url,
            'img' => $this->image ? $appURL . $this->img : '',
            'small_img' => $this->image ? $appURL . $this->small_img : '',
            'description' => $this->getTranslatedAttribute('description'),
            'body' => $this->when(Route::currentRouteName() == 'api.v2.pages.show', $this->getTranslatedAttribute('body')),
        ];
    }
}
