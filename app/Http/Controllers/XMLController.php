<?php

namespace App\Http\Controllers;

use App\Category;
use App\Helpers\Helper;
use App\Product;
use App\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use SimpleXMLElement;
use Illuminate\Support\Facades\XML;

class XMLController extends Controller
{
    public function products()
    {
         // Define the XML structure
         $xml = new \SimpleXMLElement('<feed/>');
         $xml->addAttribute('xmlns', 'http://www.w3.org/2005/Atom');

         // Prepare the data
         $products = DB::table('products')
             ->select('id', 'name', 'sku', 'category_id', 'in_stock', 'price', 'image', 'brand_id', 'created_at')
             ->get();

        // Populate the XML file with data
        foreach ($products as $product) {
            $entry = $xml->addChild('entry');
            $entry->addChild('id', $product->id);
            $entry->addChild('title', htmlspecialchars($product->name));
            $entry->addChild('link', "my link");
            $entry->addChild('description', htmlspecialchars($product->name));
            $entry->addChild('image_link', $product->image);
            $entry->addChild('brand', $product->brand_id);
            $entry->addChild('price', $product->price);
            $entry->addChild('availability', 'in stock');
            $entry->addChild('condition', 'new');
            $entry->addChild('gtin', $product->sku);
            $entry->addChild('category', $product->category_id);
            $entry->addChild('published', Carbon::parse($product->created_at)->toIso8601String());
        }

        // Save the XML file
        $xmlString = $xml->asXML();
        $filename = 'products.xml';
        $path = public_path($filename);
        file_put_contents($path, $xmlString);

        // Test the XML feed
        $xmlValidator = new \DOMDocument();
        $xmlValidator->loadXML($xmlString);


        // Submit the XML feed to Multisearch.io
        $response = FacadeResponse::make($xmlString, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
        return $response;
    }

    public function productsTwo()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><products></products>');
        $products = DB::table('products')->limit(10)->get();
        foreach ($products as $product) {
            $productElement = $xml->addChild('product');
            $productElement->addChild('id', $product->id);
            $productElement->addChild('name', htmlspecialchars($product->name));
            $productElement->addChild('sku', $product->sku);
            $productElement->addChild('category_id', $product->category_id);
            $productElement->addChild('link', URL::to('/product/' . $product->id . '-' . $product->slug));
            $productElement->addChild('in_stock', $product->in_stock);
            $productElement->addChild('price', $product->price);
            $productElement->addChild('image', URL::to('/storage/' . $product->image));
            $productElement->addChild('brand_id', $product->brand_id);
            $productElement->addChild('created_at', Carbon::parse($product->created_at)->toIso8601String());
        }

        $categories = DB::table('categories')->limit(10)->get();
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><categories></categories>');
        foreach ($categories as $product) {
            $categoryElement = $xml->addChild('category');
            $categoryElement = $xml->addChild('products');
            $categoryElement->addChild('id', $product->id);
            $categoryElement->addChild('name', htmlspecialchars($product->name));
        }

        header('Content-Type: text/xml');
        return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
    }

    public function three()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ym_catalog></ym_catalog>');
        $allCategories = Category::all();
        $product = Product::query();
        $locale = app()->getLocale();
        $products = $product->with(['categories' => function ($query) use ($locale) {
                $query->withTranslation($locale);
            }])
            ->with(['brand' => function ($query2) use ($locale) {
                $query2->withTranslation($locale);
            }])
            // ->with(['attributes' => function ($query3) use ($locale) {
            //     $query3->withTranslation($locale);
            // }])
            ->withTranslation($locale)
            ->orderBy('products.created_at', 'DESC')
            ->active()
            ->take(60000)->get();


            $shop = $xml->addChild('shop');
            $shop->addChild('name', "AllGood");
            $shop->addChild('url', "https://allgood.uz");

            $currencies = $shop->addChild('currencies');
            $currency = $currencies->addChild('currency');
            $currency->addAttribute('id', 'UZS');
            $currency->addAttribute('rate', '1');

            $categories = $shop->addChild('categories');
            foreach ($allCategories as $item) {
                $allCategoryToSingle = $categories->addChild('category', $item->name);
                $allCategoryToSingle->addAttribute('id', $item->id);
                $allCategoryToSingle->addAttribute('ordering', $item->order);
                if (($item->parent_id)) {
                    $allCategoryToSingle->addAttribute('parentId', $item->parent_id);
                }
                $allCategoryToSingle->addAttribute('url', "https://allgood.uz/category/".$item->id."-".$item->slug);
            }

            $offers = $shop->addChild('offers');

            foreach ($products as $product) {
                $offer = $offers->addChild('offer');
                if($product->in_stock != 0){
                    $offer->addAttribute('available', 'true');
                }else{
                    $offer->addAttribute('available', 'false');
                }
                $offer->addAttribute('id', $product->id);
                $offer->addChild('name', htmlspecialchars($product->name));
                $offer->addChild('url', $product->url);
                foreach ($product->categories as $key => $category) {
                    $offer->addChild('categoryId', $category->id);
                }

                if (!empty($product->brand->id)) {
                    $offer->addChild('brandId', $product->brand->id);
                } else {
                    if (!empty($product->brand->id)) {
                        $offer->addChild('brandId', $product->brand->id);
                    }else{
                        $offer->addChild('brandId', 'brand');
                    }
                }
                $offer->addChild('price', $product->price);
                if (isset($product->brand->name)) {
                    $offer->addChild('vendor', htmlspecialchars($product->brand->name));
                }
                if (!empty($product->sku)) {
                    $offer->addChild('vendorCode', htmlspecialchars($product->sku));
                }
                if ($product->in_stock == 0) {
                    $offer->addChild('presence', "Нет в наличии");
                }else{
                    $offer->addChild('presence', "Есть в наличии");
                }
                if (!empty($product->description)) {
                    $offer->addChild('description', htmlspecialchars($product->description));
                }
                $offer->addChild('ordering', $product->order);
                $offer->addChild('picture', URL::to('storage/' . $product->image));
                $offer->addChild('created_at', $product->created_at);
            }

        $xmlString = $xml->asXML();
        return response($xmlString, 200)->header('Content-Type', 'application/xml');
    }

    public function xmlgoogle1()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"></rss>');
        $product = Product::query();
        $locale = app()->getLocale();
        $shippingPrice = ShippingMethod::where('id', '1')->firstOrFail();
        $products = $product->with(['categories' => function ($query) use ($locale) {
                $query->withTranslation($locale);
            }])
            ->with(['brand' => function ($query2) use ($locale) {
                $query2->withTranslation($locale);
            }])
            ->with(['attributes' => function ($query3) use ($locale) {
                $query3->withTranslation($locale);
            }])
            ->withTranslation($locale)
            ->orderBy('products.created_at', 'DESC')
            ->active()
            ->get();
            // ->take(30000)->get();

            $channel = $xml->addChild('channel');
            $channel->addChild('title', "AllGood - Online Store");
            $channel->addChild('link', "https://allgood.uz");
            $channel->addChild('description', "This is a sample feed containing the required and recommended attributes for a variety of different products");

            foreach ($products as $key => $value) {

                $categories = $value->categories()->pluck('name')->toArray();
                $categoryString = implode(' > ', $categories);

                $item = $channel->addChild('item');

                $item->addChild('g:id', $value->id);
                if (strlen(htmlspecialchars($value->name)) >= 150) {
                    $gtitle = substr(htmlspecialchars($value->name), 0, 140) . "...";
                    $item->addChild('title', htmlspecialchars($gtitle));
                }else{
                    $item->addChild('title', htmlspecialchars($value->name));
                }
                if (!empty(htmlspecialchars($value->description))) {
                    $item->addChild('description', htmlspecialchars($value->description));
                } else {
                    $item->addChild('description', "Это описание для товара " . htmlspecialchars($value->name));
                }
                
                $item->addChild('link', $value->url);
                if (isset($value->image)) {
                    $item->addChild('g:image_link', "https://allgood.uz/storage/".$value->image);
                }else{
                    $item->addChild('g:image_link', "https://allgood.uz/storage/");
                }
                // $item->addChild('g:condition', "used");
                //$item->addChild('g:g:availability', "in_stock");
                
                if ($value->in_stock > 0) {
                    $item->addChild('g:availability', "in_stock");
                }else{
                    $item->addChild('g:availability', "out_of_stock");
                }
                $item->addChild('g:price', $value->price." UZS");
                //$item->addChild('g:g:price', "10000.00 UZS");

                $shipping = $item->addChild('g:shipping');
                $shipping->addChild('g:country', "UZ");
                $shipping->addChild('g:service', "Standard");
                $shipping->addChild('g:price', $shippingPrice->price. " UZS");

                // $item->addChild('g:gtin', "71919219405200");
                if (isset($value->brand->name)) {
                    $item->addChild('g:brand', htmlspecialchars($value->brand->name));
                }

                if (isset($value->sku)) {
                    $item->addChild('g:mpn', htmlspecialchars($value->sku));
                }

                $item->addChild('g:google_product_category', $categoryString);
                $item->addChild('g:product_type', $categoryString);
            }

        $xmlString = $xml->asXML();
        return response($xmlString, 200)->header('Content-Type', 'application/xml');
    }

    public function xmlgoogle2()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"><channel></channel></rss>');
        $product = Product::query();
        $locale = app()->getLocale();
        $shippingPrice = ShippingMethod::where('id', '1')->firstOrFail();
        $products = $product->with(['categories' => function ($query) use ($locale) {
                $query->withTranslation($locale);
            }])
            ->with(['brand' => function ($query2) use ($locale) {
                $query2->withTranslation($locale);
            }])
            ->with(['attributes' => function ($query3) use ($locale) {
                $query3->withTranslation($locale);
            }])
            ->withTranslation($locale)
            ->orderBy('products.created_at', 'DESC')
            ->active()
            ->take(2)->get();
            // ->take(30000)->get();

            $channel = $xml->channel;
            $channel->addChild('title', "AllGood - Online Store");
            $channel->addChild('description', "This is a sample feed containing the required and recommended attributes for a variety of different products");
            $channel->addChild('link', "https://allgood.uz");

            foreach ($products as $key => $value) {

                $categories = $value->categories()->pluck('name')->toArray();
                $categoryString = implode(' > ', $categories);

                $item = $channel->addChild('item');

                $item->addChild('g:id', $value->id);
                if (strlen(htmlspecialchars($value->name)) >= 150) {
                    $gtitle = substr(htmlspecialchars($value->name), 0, 140) . "...";
                    $item->addChild('title', htmlspecialchars($gtitle));
                }else{
                    $item->addChild('title', htmlspecialchars($value->name));
                }
                if (!empty(htmlspecialchars($value->description))) {
                    $item->addChild('description', htmlspecialchars($value->description));
                } else {
                    $item->addChild('description', "Это описание для товара " . htmlspecialchars($value->name));
                }
                
                $item->addChild('link', $value->url);
                if (isset($value->image)) {
                    $item->addChild('g:image_link', "https://allgood.uz/storage/".$value->image);
                }else{
                    $item->addChild('g:image_link', "https://allgood.uz/storage/");
                }
                // $item->addChild('g:condition', "used");
                //$item->addChild('g:g:availability', "in_stock");
                
                if ($value->in_stock > 0) {
                    $item->addChild('g:availability', "in_stock");
                }else{
                    $item->addChild('g:availability', "out_of_stock");
                }
                $item->addChild('g:price', $value->price." UZS");
                //$item->addChild('g:g:price', "10000.00 UZS");

                $shipping = $item->addChild('g:shipping');
                $shipping->addChild('g:country', "UZ");
                $shipping->addChild('g:service', "Standard");
                $shipping->addChild('g:price', $shippingPrice->price. " UZS");

                // $item->addChild('g:gtin', "71919219405200");
                if (isset($value->brand->name)) {
                    $item->addChild('g:brand', htmlspecialchars($value->brand->name));
                }

                if (isset($value->sku)) {
                    $item->addChild('g:mpn', htmlspecialchars($value->sku));
                }

                $item->addChild('g:google_product_category', $categoryString);
                $item->addChild('g:product_type', $categoryString);
            }

        $xmlString = $xml->asXML();
        return response($xml->asXML(), 200, [
            'Content-Type' => 'text/xml',
            'Pragma' => 'public',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    public function xmlgoogle()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"></rss>');
        $product = Product::query();
        $locale = app()->getLocale();
        $shippingPrice = ShippingMethod::where('id', '1')->firstOrFail();
        $products = $product->with(['categories' => function ($query) use ($locale) {
                $query->withTranslation($locale);
            }])
            ->with(['brand' => function ($query2) use ($locale) {
                $query2->withTranslation($locale);
            }])
            ->with(['attributes' => function ($query3) use ($locale) {
                $query3->withTranslation($locale);
            }])
            ->withTranslation($locale)
            ->orderBy('products.created_at', 'DESC')
            ->active()
            ->take(30000)->get();
            // ->take(30000)->get();

            $channel = $xml->addChild('channel');
            $channel->addChild('title', "AllGood - Online Store");
            $channel->addChild('link', "https://allgood.uz");
            $channel->addChild('description', "This is a sample feed containing the required and recommended attributes for a variety of different products");

            foreach ($products as $key => $value) {

                $categories = $value->categories()->pluck('name')->toArray();
                $categoryString = implode(' > ', $categories);

                $item = $channel->addChild('item');

                $item->addChild('g:g:id', $value->id);
                if (strlen(htmlspecialchars($value->name)) >= 150) {
                    $gtitle = substr(htmlspecialchars($value->name), 0, 140) . "...";
                    $item->addChild('title', htmlspecialchars($gtitle));
                }else{
                    $item->addChild('title', htmlspecialchars($value->name));
                }
                if (!empty(htmlspecialchars($value->description))) {
                    $item->addChild('description', htmlspecialchars($value->description));
                } else {
                    $item->addChild('description', "Это описание для товара " . htmlspecialchars($value->name));
                }
                
                $item->addChild('link', $value->url);
                if (isset($value->image)) {
                    $item->addChild('g:g:image_link', "https://allgood.uz/storage/".$value->image);
                }else{
                    $item->addChild('g:g:image_link', "https://allgood.uz/storage/");
                }
                // $item->addChild('g:condition', "used");
                //$item->addChild('g:g:availability', "in_stock");
                
                if ($value->in_stock > 0) {
                    $item->addChild('g:g:availability', "in_stock");
                }else{
                    $item->addChild('g:g:availability', "out_of_stock");
                }
                $item->addChild('g:g:price', $value->price." UZS");
                //$item->addChild('g:g:price', "10000.00 UZS");

                $shipping = $item->addChild('g:g:shipping');
                $shipping->addChild('g:g:country', "UZ");
                $shipping->addChild('g:g:service', "Standard");
                $shipping->addChild('g:g:price', $shippingPrice->price. " UZS");

                // $item->addChild('g:gtin', "71919219405200");
                if (isset($value->brand->name)) {
                    $item->addChild('g:g:brand', htmlspecialchars($value->brand->name));
                }

                if (isset($value->sku)) {
                    $item->addChild('g:g:mpn', htmlspecialchars($value->sku));
                }
                $item->addChild('g:g:update_type', "merge");

                $item->addChild('g:g:google_product_category', $categoryString);
                $item->addChild('g:g:product_type', $categoryString);
            }

        $xmlString = $xml->asXML();
        return response($xmlString, 200, [
            'Content-Type' => 'text/xml',
            'Pragma' => 'public',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
