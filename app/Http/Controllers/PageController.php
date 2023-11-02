<?php

namespace App\Http\Controllers;

use App\Helpers\Breadcrumbs;
use App\Helpers\LinkItem;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Http;
use App\Page;
use App\Review;

class PageController extends Controller
{
    /**
     * show single page
     */
    public function index($slug)
    {
        $locale = app()->getLocale();
        $defaultLocale = config('voyager.multilingual.default');
        if ($locale == $defaultLocale) {
            $page = Page::where('slug', $slug)->firstOrFail();
        } else {
            $page = Page::whereTranslation('slug', $slug)->firstOrFail();
        }


        Helper::checkModelActive($page);
        $breadcrumbs = new Breadcrumbs();

        $page->increment('views');

        if ($page->parent_id) {
            $parentPage = $page->parent;
            $parentPage = Helper::translation($parentPage);
            $breadcrumbs->addItem(new LinkItem($parentPage->name, $parentPage->url));
        }

        $siblingPages = Helper::siblingPages($page);

        $page = Helper::translation($page);

        if (!$page->parent_id) {
            $breadcrumbs->addItem(new LinkItem($page->name, $page->url, LinkItem::STATUS_INACTIVE));
        }

        return view('page.index', compact('breadcrumbs', 'page', 'siblingPages'));
    }

    public function print(Page $page)
    {
        $page = Helper::translation($page);
        return view('page.print', compact('page'));
    }

    public function guestbook()
    {
        $page = Helper::translation(Page::findOrFail(20));

        $reviews = Review::active()->where('reviewable_type', Page::class)->where('reviewable_id', 1)->paginate(20);

        $links = $reviews->links();

        return view('page.guestbook', compact('page', 'reviews', 'links'));
    }

    public function calculator() {
        return view('page.calculator');
    }

    public function calculatorKundalik() {
        return view('page.kundalik');
    }

    public function sendToTelegram(Request $request) {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'fathername' => 'required',
            'pinfl' => 'required',
            'passport' => 'required',
            'phone_number' => 'required',
            'region' => 'required',
        ]);

        function custom_format_number($number) {
            if ($number >= 1000000) {
                return number_format($number / 1000000, 0, '.', ' ') . ' 000 000';
            } else {
                return number_format($number, 0, '.', ' ');
            }
        }

        $source = "AllGood";
        $hiddenKundalik = '';
        if ($request->hiddenKundalik) {
            $hiddenKundalik = " | Kundalik";
            $source = "AllGood-K";
            
        }

        // SEND DATA TO ALLGOODNASIYA
        $data = [
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'fathername' => $request->input('fathername'),
            'pinfl' => $request->input('pinfl'),
            'passport' => $request->input('passport'),
            'region' => $request->input('region'),
            'phone_number' => $request->input('phone_number'),
            'bin_code_or_card' => $request->input('card_number'),
            'expiry' => $request->input('expiry'),
            'source' => $source,
        ];
    
        $response = Http::post('https://allgoodnasiya.uz/api/v1/send-data', $data);
        $result = json_decode($response->body());

        /*
        $botToken = "6020109850:AAG3CV00uRopavR-QgOnmD9SYqEsr9tV-8M";
        $chat_id = "@algdscrngfrlds";
        $smile = '👉';
        //$link = "http://extab.uz/fullpage.php?id=".$idd;
        $caption = "Источник: allgood.uz" . $hiddenKundalik
        .PHP_EOL."Ф.И.О: ".$request->name . " " . $request->lastname . " " . $request->fathername
        .PHP_EOL."ПИНФЛ: ".$request->pinfl
        .PHP_EOL."Лимит: ".Custom_format_number($request->limit)
        .PHP_EOL."Паспорт: ".$request->passport
        .PHP_EOL."Область: ".$request->region
        .PHP_EOL."Тел: ".$request->phone_number
        .PHP_EOL."Карта: ".$request->card_number
        .PHP_EOL."Срок: ".$request->expiry
        .PHP_EOL."Комментарий: ".$request->comment
        .PHP_EOL.PHP_EOL."Перейти: https://allgoodnasiya.uz/merchant/calculator/list/show/".$result;//$smile.' '.$link;
        $photo = "https://c4.wallpaperflare.com/wallpaper/394/308/979/leonardo-dicaprio-leonardo-dicaprio-the-wolf-of-wall-street-jordan-belfort-wallpaper-preview.jpg";
        $bot_url = "https://api.telegram.org/bot$botToken/";
        $url = $bot_url."sendPhoto?chat_id=".$chat_id."&photo=".urlencode($photo)."&caption=".urlencode($caption);
        file_get_contents($url);
        */

        
        return redirect()->route('my.calculator.finish');
        // return redirect()->back()->with('success', __("main.form_10"));
    }

    public function calculatorFinish()
    {
        return view('page.calculator_finish');
    }
}
