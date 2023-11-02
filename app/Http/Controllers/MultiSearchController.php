<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MultiSearchController extends Controller
{
    public function index(Request $request)
    {
        $data = Http::get("https://api.multisearch.io/?id=12222&query=".$request->value."&uid=cc441f080&limit=8&offset=0&categories=0&fields=name,url");
        $items = json_decode($data->body(), true);

        $search = Http::get("https://api.multisearch.io/?id=12222&uid=cc441f080");
        $search = json_decode($search->body(), true);

        return view('multisearch.list', compact('items', 'search'));
    }
}
