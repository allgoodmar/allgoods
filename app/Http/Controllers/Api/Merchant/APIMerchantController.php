<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Product;
use App\SellerCompany;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use function PHPSTORM_META\map;

class APIMerchantController extends Controller
{
    public function create_token(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('phone_number', $request->input('phone_number'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'error' => 'The provided credentials are incorrect.'
            ]);
        }

        $token = $user->createToken($request->input('device_name'))->plainTextToken;

        /*
        SellerCompany::where('phone_number', $request->input('phone_number'))->update([
            'token' => $token,
            'device_name' => $request->input('device_name')
        ]);
        */

        User::where('phone_number', $request->input('phone_number'))->update([
            'access_token' => $token,
            'device_name' => $request->input('device_name')
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function getProducts($company_id)
    {
        $product = Product::where('company_id', $company_id)->orderBy('id', 'DESC')->get();
        return $product;
    }

    public function storeProducts(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'name' => 'required',
            'sku' => 'required',
            'price' => 'required',
            'in_stock' => 'required'
        ]);

        if (!$request->token) {
            return response()->json([
                'status' => false,
                'message' => "You didn't add token in the field!"
            ]);
        }

        $token = $request->token;
        $seller = SellerCompany::where('token', $token)->first();

        $barcode = !empty($request->barcode) ? $barcode = $request->barcode : $barcode = null;
        $description = !empty($request->description) ? $description = $request->description : $description = null;
        $sale_price = !empty($request->sale_price) ? $sale_price = $request->sale_price : $sale_price = 0;
        $sale_end_date = !empty($request->sale_end_date) ? $sale_end_date = $request->sale_end_date : $sale_end_date = null;

        $check = Product::where([['external_id', $request->product_id],['sku', $request->sku]])->first();

        if (empty($check)) {
            $p = Product::create([
                'seller_id' => $seller->id,
                'external_id' => $request->product_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'sku' => $request->sku,
                'barcode' => $barcode, // optional
                'description' => $description, // optional
                'price' => $request->price,
                'sale_price' => $sale_price, // optional
                'sale_end_date' => $sale_end_date,
                'in_stock' => $request->in_stock,
                'status' => Product::STATUS_INACTIVE,
                'views' => 0,
                'order' => 990
            ]);
        }else{
            $p = Product::where([['external_id', $request->product_id],['sku', $request->sku]])->update([
                'seller_id' => $seller->id,
                'external_id' => $request->product_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'sku' => $request->sku,
                'barcode' => $barcode, // optional
                'description' => $description, // optional
                'price' => $request->price,
                'sale_price' => $sale_price, // optional
                'sale_end_date' => $sale_end_date,
                'in_stock' => $request->in_stock,
                'status' => Product::STATUS_INACTIVE,
                'views' => 0,
                'order' => 990
            ]);
        }

        if (!empty($request->image)) {
            Helper::storeImageFromUrl($request->image, $p, 'image', 'products', Product::$imgSizes);
        }

        if (!empty($request->images)) {
            Helper::storeImagesFromUrl($request->images, $p, 'images', 'products', Product::$imgSizes);
        }

        return response()->json([
            'status' => true,
            'message' => "Products has been successfully added!"
        ]);
    }
}
