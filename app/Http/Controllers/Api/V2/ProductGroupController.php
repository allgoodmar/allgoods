<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductGroupResource;
use App\ProductGroup;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    public function show(Request $request, ProductGroup $productGroup)
    {
        $locale = app()->getLocale();
        $productGroup->load('translations');
        $productGroup->load(['products' => function($query) use ($locale) {
            $query->active()->withTranslation($locale);
        }]);
        $productGroup->load(['attributeValues' => function($query) use ($locale) {
            $query->withTranslation($locale);
        }]);
        $productGroup->load(['attributes' => function($query) use ($locale) {
            $query->withTranslation($locale);
        }]);
        return new ProductGroupResource($productGroup);
    }
}
