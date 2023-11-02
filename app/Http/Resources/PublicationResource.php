<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class PublicationResource extends JsonResource
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
            'type' => $this->type,
            'name' => $this->getTranslatedAttribute('name'),
            'url' => $this->url,
            'img' => $this->image ? $appURL . $this->img : '',
            'medium_img' => $this->image ? $appURL . $this->medium_img : '',
            'description' => $this->getTranslatedAttribute('description'),
            'body' => $this->when(Route::currentRouteName() == 'api.v2.publications.show', $this->getTranslatedAttribute('body')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
