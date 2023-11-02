<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class CategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'order' => $this->order,
            'name' => $this->getTranslatedAttribute('name'),
            'full_name' => $this->full_name,
            'hierarchy_name' => $this->hierarchy_name,
            'url' => $this->url,
            'img' => $this->image ? $appURL . $this->img : '',
            'small_img' => $this->image ? $appURL . $this->small_img : '',
            'description' => $this->getTranslatedAttribute('description'),
            'body' => $this->when(Route::currentRouteName() == 'api.v2.categories.show', $this->getTranslatedAttribute('body')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
