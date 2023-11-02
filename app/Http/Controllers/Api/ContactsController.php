<?php

namespace App\Http\Controllers\Api;

use App\Product;
use App\StaticText;
use Illuminate\Http\Request;

class ContactsController extends ApiController
{
    public function index(Request $request)
    {
        $data = [
            'site' => config('app.url'),
            'site_title' => setting('site.title'),
            'phone' => setting('contact.phone'),
            'email' => setting('contact.email'),
            'address' => StaticText::where('key', 'contact_address')->first()->translate()->description,
        ];
        return $data;
    }
}
