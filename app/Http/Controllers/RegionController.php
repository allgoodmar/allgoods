<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class RegionController extends Controller
{
    public function set(Request $request)
    {
        $data = $this->validate($request, [
            'region_id' => 'required|exists:regions,id',
        ]);

        $cookie = cookie('region_id', $data['region_id'], 60 * 24 * 30 * 6);

        $data = [
            'message' => __('main.form.regions_list_form_success'),
        ];

        return response()->json($data)->cookie($cookie);
    }
}
