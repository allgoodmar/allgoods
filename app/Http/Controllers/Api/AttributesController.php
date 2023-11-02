<?php

namespace App\Http\Controllers\Api;

use App\Attribute;
use Illuminate\Http\Request;

class AttributesController extends ApiController
{
    public function index()
    {
        return Attribute::all();
    }

    public function attributeValues(Attribute $attribute)
    {
        return $attribute->attributeValues;
    }

    public function show(Attribute $attribute)
    {
        return $attribute;
    }
}
