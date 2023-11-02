<?php

namespace App\Http\Controllers;

use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Order;
use App\Product;
use App\Review;
use App\SellerCompany;
use App\SellerCompanyOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Spatie\SchemaOrg\AggregateRating;
use Spatie\SchemaOrg\Schema;

class SellerController extends Controller
{
    public function account($owner = null)
    {

        if (isset(auth()->user()->id)) {
            $owner = auth()->user()->id;
            Session::put('owner_id_login', $owner);
        }else{
            Session::put('owner_id_login', $owner);
        }

        $check = SellerCompany::where('owner_id', $owner)->first();
        $seller_owner = SellerCompanyOwner::where('id', $owner)->first();

        if ($check->status == 0) {
            return redirect()->route('logout');
        }

        if (!empty(Session::get('owner_id_login'))) {
            Session::put('seller_owner_name', auth()->user()->name);
            Session::put('seller_owner_phone_number', auth()->user()->phone_number);

            Session::put('seller_company_id', $check->id);
            Session::put('seller_company_name', $check->company_name);
            Session::put('seller_company_inn', $check->company_inn);
            Session::put('seller_company_oked', $check->company_oked);
            Session::put('seller_company_official_name', $check->company_official_name);
            Session::put('seller_company_checking_account', $check->company_checking_account);
            Session::put('seller_company_bank_code_mfo', $check->company_bank_code_mfo);
            Session::put('seller_company_bank_name', $check->company_bank_name);
            Session::put('seller_company_phone_number', $check->company_phone_number);
        }else{
            return dd("Something went wrong!");
        }

        $seller_company = $check;

        $revenue = 0;
        $percentage = 0;
        $profit = 0;
        $check_seller_order = Order::where('seller_id', $check->id)->get();
        if (!empty($check_seller_order)) {
            $revenue = Order::where('seller_id', $check->id)->sum("subtotal");
            $percentage = ($revenue / 100) * 5;
            $profit = $revenue - $percentage;
        }

        $seller_orders = Order::select(
            "orders.*",
            DB::raw("order_items.name as product_name"),
            DB::raw("order_items.quantity"),
            DB::raw("order_items.price"),
            DB::raw("order_items.created_at as order_item_created_at")
            )
        ->where('orders.seller_id', $check->id)
        ->join("order_items", "order_items.order_id", "=", "orders.id")
        ->orderBy("orders.id", "DESC")
        ->paginate(50);

        $ordersCount = Order::select(
            "orders.*",
            DB::raw("order_items.name as product_name"),
            DB::raw("order_items.quantity"),
            DB::raw("order_items.price")
            )
        ->where('orders.seller_id', $check->id)
        ->join("order_items", "order_items.order_id", "=", "orders.id")
        ->orderBy("orders.id", "DESC")
        ->count();

        $compact = compact('seller_owner', 'seller_company', 'revenue', 'percentage', 'profit', 'seller_orders', 'ordersCount');

        return view('seller.profile.account', $compact);
    }

    public function products()
    {
        if (empty(Session::get('owner_id_login'))) {
            return redirect()->to('/');
        }

        $locale = app()->getLocale();
        $product = Product::where('seller_id', Session::get('seller_company_id'));
        $productCount = Product::where('seller_id', Session::get('seller_company_id'))->count();

        $products = $product
            ->withTranslation($locale)
            ->orderBy("created_at", "DESC")
            ->paginate(100);

        // $products = $products->get();

        $compact = compact('products', 'productCount');

        return view('seller.profile.products', $compact);
    }

    public function productsEdit($id)
    {
        $product = Product::where('id', $id)->first();

        $before = strtok($product->sale_end_date, ' ');
        $after = substr($product->sale_end_date, strpos($product->sale_end_date, " ") + 1);

        return view("seller.profile.products-edit", compact('product', 'before', 'after'));
    }

    public function productsUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required'
        ]);

        if (!empty($request->barcode)) {
            $barcode = $request->barcode;
        }else{
            $barcode = null;
        }

        if (!empty($request->quantity)) {
            $quantity = $request->quantity;
        }else{
            $quantity = 0;
        }

        $sale_end_timestamp = null;
        if ($request->sale_price != 0.00) {
            $sale_price = $request->sale_price;
            if (!empty($request->sale_end_date) && !empty($request->sale_end_time)) {
                $sale_end_timestamp = $request->sale_end_date . " " . $request->sale_end_time;
            }
        }else{
            $sale_price = 0;
        }

        Product::where('id', $id)->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'sale_price' => $sale_price,
            'sale_end_date' => $sale_end_timestamp,
            'status' => $request->status,
            'is_bestseller' => $request->is_bestseller,
            'is_new' => $request->is_new,
            'is_promotion' => $request->is_promotion,
            'in_stock' => $quantity
        ]);

        $product = Product::where('id', $id)->first();

        if (!empty($request->image)) {
            Helper::storeImage($product, 'image', 'products', Product::$imgSizes);
        }

        if (!empty($request->images)) {
            Helper::storeImages($request->images, $product, 'images', 'products', Product::$imgSizes);
        }


        return back()->with('success', 'Muvaffaqiyatli tahrirlandi!');
    }

    public function changestatustoyes(Request $request)
    {
        Product::where('id', $request->id)->update([
            'status' => 1
        ]);

        return true;
    }

    public function changestatustono(Request $request)
    {
        Product::where('id', $request->id)->update([
            'status' => 0
        ]);

        return true;
    }

    public function visit($id)
    {
        $seller = SellerCompany::where('id', $id)->first();

        $locale = app()->getLocale();
        $breadcrumbs = new Breadcrumbs();

        $product = Product::query();

        $products = $product
            ->where([['seller_id', $id],['status', 1]])
            ->withTranslation($locale)
            ->orderBy('views', 'DESC');
        $products = $products->get();
        $productsCount = $products->count();

        $ordersCount = Order::where('seller_id', $id)->count();

        return view('seller.profile.visit', compact('breadcrumbs', 'products', 'seller', 'productsCount', 'ordersCount'));
    }

    public function uploadImages(Request $request, $id)
    {
        $images = $request->images;

        $image = array_shift($images);
        $imagesJson = json_encode($images);

        $p = Product::where('id', $id)->first();

        Helper::storeImageFromUrl($image, $p, 'image', 'products', Product::$imgSizes);

        Helper::storeImagesFromUrl($imagesJson, $p, 'images', 'products', Product::$imgSizes);
        return true;
    }
}