<?php

namespace App\Helpers;

use App\AtmosTransaction;
use App\Banner;
use App\BannerStats;
use App\Card;
use App\Category;
use App\Order;
use App\Otp;
use App\Page;
use App\Partner;
use App\PartnerInstallment;
use App\PaymentMethod;
use App\Product;
use App\Region;
use App\Review;
use App\Services\AtmosService;
use App\Services\TrendyolService;
use App\Setting;
use App\StaticText;
use App\User;
use Browser;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Parsedown;
use TCG\Voyager\Facades\Voyager;
use Throwable;

class Helper
{

    public static function formatNumber($number)
    {
        return number_format($number, 0, '.', ' ');
    }

    public static function formatPriceHuman($number)
    {
        $returnNumber = $number;
        $format = '';
        if ($number >= 1000000000) {
            $returnNumber /= 1000000000;
            $format = __('main.bil_formatted');
        } elseif ($number >= 1000000) {
            $returnNumber /= 1000000;
            $format = __('main.mill_formatted');
        } elseif ($number >= 1000) {
            $returnNumber /= 1000;
            $format = __('main.thous_formatted');
        }
        $returnNumber = floatval(round($returnNumber, 1));
        if ($format) {
            $returnNumber = __($format, ['number' => $returnNumber]);
        }
        $returnNumber .= ' ' . __('main.currency');
        return $returnNumber;
    }

    public static function formatPrice($number)
    {
        return self::formatNumber($number) . ' ' . __('main.currency');
    }

    public static function formatPriceWithoutCurrency($number)
    {
        return self::formatNumber($number) . ' UZS';
    }

    public static function formatDate(Carbon $date, $year = false)
    {
        $yearFormat = ($year) ? ', Y' : '';
        return __($date->format('F')) . ' ' . $date->format('d' . $yearFormat);
    }

    public static function formatDateSecond(Carbon $date)
    {
        return '<div>' . $date->format('d') . '</div><div>' . __($date->format('F')) . '</div>';
    }

    public static function formatViews($views = 0)
    {
        $text = $views . ' <span class="d-none d-lg-inline">';
        if (Str::endsWith($views, [11, 12, 13, 14])) {
            $text .= 'просмотров';
        } elseif (Str::endsWith($views, [2, 3, 4])) {
            $text .= 'просмотра';
        } elseif (Str::endsWith($views, 1)) {
            $text .= 'просмотр';
        } else {
            $text .= 'просмотров';
        }
        return $text . '</span>';
    }

    public static function formatOffers($offers = 0)
    {
        $text = $offers . ' ';
        if (Str::endsWith($offers, [11, 12, 13, 14])) {
            $text .= 'предложений';
        } elseif (Str::endsWith($offers, [2, 3, 4])) {
            $text .= 'предложения';
        } elseif (Str::endsWith($offers, 1)) {
            $text .= 'предложение';
        } else {
            $text .= 'предложений';
        }
        return $text;
    }

    public static function menuItems($type = '')
    {
//        $menu = [];
        $menu =   Cache::remember($type.'_cache_'.app()->getLocale(), config('params.menuItems'), function () use ($type) {
            $query = Page::active();
            if ($type == 'header') {
                $query->inHeaderMenu();
            } elseif ($type == 'footer') {
                $query->inFooterMenu();
            }
            $pages = $query->whereNull('parent_id')->orderBy('order')->with(['pages' => function ($q) {
                $q->active()->with(['pages' => function ($q1) {
                    $q1->active()->withTranslation(app()->getLocale());
                }])->withTranslation(app()->getLocale());
            }])->withTranslation(app()->getLocale())->get();
            if (!$pages->isEmpty()) {
                $pages = $pages->translate();
                foreach ($pages as $page) {
                    $item = new MenuItem(new LinkItem($page->short_name_text, $page->url));
                    $subPages = $page->pages;
                    if (!$subPages->isEmpty()) {
                        $subPages = $subPages->translate();
                        foreach ($subPages as $subPage) {
                            $subPageItem = new MenuItem(new LinkItem($subPage->short_name_text, $subPage->url));
                            $subPagePages = $subPage->pages;
                            if (!$subPagePages->isEmpty()) {
                                $subPagePages = $subPagePages->translate();
                                foreach ($subPagePages as $subPagePage) {
                                    $subPagePageItem = new MenuItem(new LinkItem($subPagePage->short_name_text, $subPagePage->url));
                                    $subPageItem->addItem($subPagePageItem);
                                }
                            }
                            $item->addItem($subPageItem);
                        }
                    }
                    $menu[] = $item;
                }
            }
            return $menu;
        });
        return $menu;
    }

    public static function categories($type = 'parents', $limit = 0)
    {

        $categories =  Cache::remember($type.'_'.$limit.app()->getLocale(), config('params.categories'), function () use ($type, $limit) {
            $showIn = false;
            if ($type == 'menu') {
                $showIn = [Category::SHOW_IN_MENU, Category::SHOW_IN_EVERYWHERE];
            }
            if ($type == 'home') {
                $showIn = [Category::SHOW_IN_HOME, Category::SHOW_IN_EVERYWHERE];
            }
            $locale = app()->getLocale();
            $query = Category::active()
                ->withTranslation($locale)
                ->orderBy('order');
            if ($showIn) {
                $query->whereIn('show_in', $showIn);
            }
            if ($type == 'menu') {
                $query
                    ->whereNull('parent_id')
                    ->with(['children' => function ($q1) use ($locale, $showIn) {
                        $q1
                            ->active()
                            ->withTranslation($locale)
                            ->whereIn('show_in', $showIn)
                            ->orderBy('order')
                            ->with(['children' => function ($q2) use ($locale, $showIn) {
                                $q2
                                    ->active()
                                    ->withTranslation($locale)
                                    ->orderBy('order')
                                    ->whereIn('show_in', $showIn);
                            }]);
                    }]);
            }
            if ($type == 'parents') {
                $query
                    ->whereNull('parent_id')
                    ->with(['children' => function ($q1) use ($locale) {
                        $q1
                            ->active()
                            ->withTranslation($locale)
                            ->orderBy('order')
                            ->with(['children' => function ($q2) use ($locale) {
                                $q2
                                    ->active()
                                    ->withTranslation($locale)
                                    ->orderBy('order');
                            }]);
                    }]);
            }
            if ($limit > 0) {
                $query->take($limit);
            }
            $query->with(['banners' => function($q1) use ($locale) {
                $q1->withTranslation($locale);
            }]);
            $categories = $query->get();
            if (!$categories->isEmpty()) {
                $categories = $categories->translate();
            }
            return $categories;
        });
        return $categories;
    }

    public static function banner($type)
    {
        $banner =   Cache::remember($type.'_banner_'.app()->getLocale(), config('params.banner'), function () use($type) {
            $banner = Banner::where('type', $type)->whereNull('category_id')->active()->nowActive()->withTranslation(app()->getLocale())->latest()->first();
            if (!$banner) {
                $banner = Banner::where([['type', $type], ['shop_id', null], ['category_id', null]])->withTranslation(app()->getLocale())->active()->latest()->first();
            }
            if (!$banner) {
                $banner = new Banner(['id' => 0, 'name' => '1', 'url' => '', 'image' => 'no-image.jpg']);
            }
            $banner = $banner->translate();
            return $banner;
        });
        return $banner;
    }

    public static function banners($type)
    {
        $banners =   Cache::remember($type.'_banners_'.app()->getLocale(), config('params.banners'), function () use($type) {
            $banners = Banner::where('type', $type)->whereNull('category_id')->active()->withTranslation(app()->getLocale())->latest()->get();
            if (!$banners) {
                $banners = Banner::where([['type', $type], ['shop_id', null], ['category_id', null]])->active()->withTranslation(app()->getLocale())->latest()->get();
            }
            $banners = $banners->translate();
            return $banners;
        });
        return $banners;
    }

    public static function markdownToHtml($string)
    {
        $parsedown = new Parsedown();
        return $parsedown->text($string);
    }
    /**
     * Update currencies
     *
     * @return void
     */
    public static function updateCurrencies()
    {
        $currencies = Setting::whereIn('key', ['currency.usd', 'currency.eur'])->get();
        foreach ($currencies as $currency) {
            $currencyShortCode = mb_strtoupper(str_replace('currency.', '', $currency->key));
            try {
                $client = new Client([
                    'base_uri' => 'http://cbu.uz/uzc/arkhiv-kursov-valyut/',
                    'timeout' => 2.0,
                ]);
                $response = $client->get('xml/' . $currencyShortCode . '/');
                $xml = new \SimpleXMLElement($response->getBody()->getContents());
                $currency->value = $xml->CcyNtry->Rate;
                $currency->save();
            } catch (\Exception $e) {
            }
        }
    }

    public static function phone($phone)
    {
        $phone = preg_replace('#[^\d]#', '', $phone);
        if (Str::startsWith($phone, '998')) {
            $phone = '+' . $phone;
        }
        return $phone;
    }

    public static function reformatPhone($phone)
    {
        $phone = preg_replace('#[^\d]#', '', $phone);
        if (mb_strlen($phone) == 9) {
            $phone = '998' . $phone;
        }
        return $phone;
    }

    public static function parsePhones($phones)
    {
        $parsed = [];
        $phones = str_replace([';'], ',', $phones);
        $phones = explode(',', $phones);
        foreach ($phones as $phone) {
            $parsed[] = [
                'original' => $phone,
                'clean' => self::phone($phone),
            ];
        }
        return $parsed;
    }

    public static function reformatText($text)
    {
        return preg_replace(['#\*(.*?)\*#', '#\#(.*?)\##', '#\|\|#'], ['<strong>$1</strong>', '<span class="text-primary">$1</span>', '<br>'], $text);
    }

    public static function formatWorkDays($days)
    {
        $days = explode(',', preg_replace('#[^0-9,]#', '', $days));
        $days = array_map('intval', $days);
        $daysStatus = [];
        for ($i = 1; $i <= 7; $i++) {
            $daysStatus[$i] = in_array($i, $days) ? true : false;
        }
        return $daysStatus;
    }

    /**
     *
     * @return array
     */
    public static function languageSwitcher()
    {
        $route = Route::current();
        $routeName = Route::currentRouteName();
        $switcher = new LanguageSwitcher();
        $currentLocale = app()->getLocale();
        $defaultLocale = config('voyager.multilingual.default');

        $model = null;
        $foundModel = false;
        $hasSlugRoutes = ['page', 'category', 'publications.show', 'product'];
        foreach ($hasSlugRoutes as $hasSlugRoute) {
            if ($routeName == $hasSlugRoute) {
                $routeParams = array_values($route->parameters);
                $model = array_shift($routeParams);
                if (!is_object($model)) {
                    continue;
                }
                $foundModel = true;
                break;
            }
        }

        $onlySlugRoutes = ['page' => Page::class];
        foreach ($onlySlugRoutes as $key => $modelClass) {
            if ($routeName == $key) {
                $routeParams = array_values($route->parameters);
                $slug = array_shift($routeParams);
                if ($currentLocale == $defaultLocale) {
                    $model = $modelClass::where('slug', $slug)->first();
                } else {
                    $model = $modelClass::whereTranslation('slug', $slug)->first();
                }
                if (!is_object($model)) {
                    continue;
                }
                $foundModel = true;
                break;
            }
        }

        if ($foundModel && $model) {
            foreach (config('laravellocalization.supportedLocales') as $key => $value) {
                $model = $model->translate($key);
                $value['url'] = $model->getModel()->getURL($key);
                $linkItem = new LinkItem($value['native'], $value['url']);
                $linkItem->key = $key;
                if ($key == $currentLocale) {
                    $switcher->setActive($linkItem);
                }
                if ($key == $defaultLocale) {
                    $switcher->setMain($linkItem);
                }
                $switcher->addValue($linkItem);
            }
        } else {
            $url = url()->current();
            foreach (config('laravellocalization.supportedLocales') as $key => $value) {
                $value['url'] = LaravelLocalization::localizeURL($url, $key);
                $linkItem = new LinkItem($value['native'], $value['url']);
                $linkItem->key = $key;
                if ($key == $currentLocale) {
                    $switcher->setActive($linkItem);
                }
                if ($key == $defaultLocale) {
                    $switcher->setMain($linkItem);
                }
                $switcher->addValue($linkItem);
            }
        }

        return $switcher;
    }

    public static function getActiveLanguageRegional()
    {
        $currentLocale = app()->getLocale();
        $locales = config('laravellocalization.supportedLocales');
        return $locales[$currentLocale]['regional'];
    }

    /**
     *
     * @return App\CustomSetting
     */
    public static function getSettings()
    {
        $settings = CustomSetting::findOrFail(1);
        $settings = self::translation($settings);
        return $settings;
    }

    public static function translation($model)
    {
        if (app()->getLocale() != config('voyager.multilingual.default')) {
            return $model->translate();
        }
        return $model;
    }

    /**
     * Send message via telegram bot to group
     */
    public static function toTelegram($text, $parse_mode = 'HTML', $chat_id = '')
    {
        $token = config('services.telegram.bot_token');

        if (!$chat_id) {
            $chat_id = config('services.telegram.chat_id');
        }

        $formData = [];
        $formData['chat_id'] = $chat_id;
        $formData['text'] = $text;
        if (in_array($parse_mode, ['HTML', 'Markdown'])) {
            $formData['parse_mode'] = $parse_mode;
        }

        try {
            $client = new Client([
                'base_uri' => 'https://api.telegram.org',
                'timeout' => 2.0,
            ]);

            $client->post('/bot' . $token . '/sendMessage', [
                'form_params' => $formData,
            ]);
        } catch (Exception $e) {
        }
    }

    /**
     * Send message via telegram bot to group
     */
    public static function documentToTelegram($filePath, $fileName, $caption, $parse_mode = 'HTML', $chat_id = '')
    {
        $token = config('services.telegram.bot_token');

        if (!$chat_id) {
            $chat_id = config('services.telegram.chat_id');
        }
        if (!in_array($parse_mode, ['HTML', 'Markdown'])) {
            $parse_mode = 'HTML';
        }

        $multiparData = [
            [
                'name'     => 'chat_id',
                'contents' => $chat_id,
            ],
            [
                'name'     => 'caption',
                'contents' => $caption,
            ],
            [
                'name'     => 'document',
                'contents' => fopen($filePath, 'r'),
                'filename' => $fileName,
            ],
            [
                'name'     => 'parse_mode',
                'contents' => $parse_mode,
            ],
        ];

        try {
            $client = new Client([
                'base_uri' => 'https://api.telegram.org',
                'timeout'  => 2.0,
            ]);

            $result = $client->post('/bot' . $token . '/sendDocument', [
                'multipart' => $multiparData,
            ]);
            // Log::info($result);
        } catch (Exception $e) {
            Log::info(print_r($e, true));
        }
    }

    public static function storeFile($model, $requestField, $dir, $isImage = false, $modelField = '')
    {
        if (request()->has($requestField)) {
            if (!$modelField) {
                $modelField = $requestField;
            }
            $url = request()->$requestField->store($dir . '/' . date('FY'), 'public');
            if (!$isImage) {
                $url = json_encode([
                    [
                        'download_link' => $url,
                        'original_name' => request()->$requestField->getClientOriginalName(),
                    ]
                ]);
            }
            $model->$modelField = $url;
            $model->save();
        }
        return $model;
    }

    public static function storeImage($model, $requestField, $dir, $thumbs = [], $modelField = '')
    {
        $model = self::storeFile($model, $requestField, $dir, true, $modelField);
        if (!$modelField) {
            $modelField = $requestField;
        }
        if ($thumbs && $model->$modelField) {
            $image = Image::make(storage_path('app/public/' . $model->$modelField));
            $image->backup();
            if ($image) {
                $ext = mb_strrchr($model->$modelField, '.');
                $pos = mb_strrpos($model->$modelField, '.');
                $fileName = mb_substr($model->$modelField, 0, $pos);
                foreach ($thumbs as $key => $value) {
                    $image->fit($value[0], $value[1])->save(storage_path('app/public/' . $fileName . '-' . $key . $ext));
                    $image->reset();
                }
            }
        }
        return $model;
    }

    public static function deleteImage($model, $field, $thumbs = [])
    {
        if ($model->$field) {
            $ext = mb_strrchr($model->$field, '.');
            $pos = mb_strrpos($model->$field, '.');
            $fileName = mb_substr($model->$field, 0, $pos);
            if ($thumbs) {
                foreach ($thumbs as $key => $value) {
                    Storage::disk('public')->delete($fileName . '-' . $key . $ext);
                }
            }
            Storage::disk('public')->delete($model->$field);
            $model->$field = '';
            $model->save();
        }
    }

    public static function storeImageFromUrl($url, $model, $field, $dir, $thumbs = [])
    {
        // check name
        $name = substr($url, strrpos($url, '/') + 1);
        // no extension
        if (strpos($name, '.') === false) {
            return $model;
        }

        // get contents
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        try {
            $contents = file_get_contents($url, false, stream_context_create($arrContextOptions));
        } catch (Throwable $th) {
            return $model;
        }

        // save file
        $fieldValue = $dir . '/' . date('FY') . '/' . $model->id . '/' . $name;
        Storage::disk('public')->put($fieldValue, $contents);

        // check mime type
        $mime = mime_content_type(Storage::disk('public')->path($fieldValue));
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
            Storage::disk('public')->delete($fieldValue);
            return $model;
        }

        // save model
        $model->$field = $fieldValue;
        $model->save();

        // create thumbs
        if ($thumbs && $model->$field) {
            $image = Image::make(storage_path('app/public/' . $model->$field));
            $image->backup();
            if ($image) {
                $ext = mb_strrchr($model->$field, '.');
                $pos = mb_strrpos($model->$field, '.');
                $fileName = mb_substr($model->$field, 0, $pos);
                foreach ($thumbs as $key => $value) {
                    $image->fit($value[0], $value[1])->save(storage_path('app/public/' . $fileName . '-' . $key . $ext));
                    $image->reset();
                }
            }
        }
        return $model;
    }

    public static function storeImagesFromUrl($urls, $model, $field, $dir, $thumbs = [])
    {
        $fieldValues = [];
        foreach ($urls as $url) {
            $contents = file_get_contents($url);
            $name = substr($url, strrpos($url, '/') + 1);
            // no extension
            if (strpos($name, '.') === false) {
                continue;
            }
            // save file
            $fieldValue = $dir . '/' . date('FY') . '/' . $model->id . '/' . $name;
            Storage::disk('public')->put($fieldValue, $contents);

            $mime = mime_content_type(Storage::disk('public')->path($fieldValue));
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                Storage::disk('public')->delete($fieldValue);
                continue;
            }
            $fieldValues[] = $fieldValue;
        }

        $model->$field = json_encode($fieldValues);
        $model->save();

        if ($thumbs && !empty($model->$field) && $model->$field != '[]') {
            $modelImages = json_decode($model->$field);
            foreach ($modelImages as $modelImage) {
                $image = Image::make(storage_path('app/public/' . $modelImage));
                $image->backup();
                if ($image) {
                    $ext = mb_strrchr($modelImage, '.');
                    $pos = mb_strrpos($modelImage, '.');
                    $fileName = mb_substr($modelImage, 0, $pos);
                    foreach ($thumbs as $key => $value) {
                        $image->fit($value[0], $value[1])->save(storage_path('app/public/' . $fileName . '-' . $key . $ext));
                        $image->reset();
                    }
                }
            }
        }
        return $model;
    }

    public static function createModelImage($imagePath, $model, $dir, $thumbs = [])
    {
        $newImageDir = storage_path('app/public/') . $dir . '/' . date('FY') . '/';
        if (!is_dir($newImageDir)) {
            mkdir($newImageDir, 0755, true);
        }
        $fileBaseName = basename($imagePath);
        $newImagePath = $newImageDir . $fileBaseName;
        $result = File::move($imagePath, $newImagePath);
        if ($result) {
            $modelImage = $dir . '/' . date('FY') . '/' . $fileBaseName;
            $model->image = $modelImage;
            $model->save();
            if ($thumbs) {
                $image = Image::make($newImagePath);
                $image->backup();
                if ($image) {
                    $ext = mb_strrchr($fileBaseName, '.');
                    $pos = mb_strrpos($fileBaseName, '.');
                    $fileName = mb_substr($fileBaseName, 0, $pos);
                    foreach ($thumbs as $key => $value) {
                        $image->fit($value[0], $value[1])->save($newImageDir . $fileName . '-' . $key . $ext);
                        $image->reset();
                    }
                }
            }
        }
        return $model;
    }

    public static function createModelImages($imagePaths, $model, $dir, $thumbs = [])
    {
        $savedImages = [];
        foreach ($imagePaths as $imagePath) {
            $newImageDir = storage_path('app/public/') . $dir . '/' . date('FY') . '/';
            if (!is_dir($newImageDir)) {
                mkdir($newImageDir, 0755, true);
            }
            $fileBaseName = basename($imagePath);
            $newImagePath = $newImageDir . $fileBaseName;
            $result = File::move($imagePath, $newImagePath);
            if ($result) {
                $savedImages[] = [
                    'newImageDir' => $newImageDir,
                    'fileBaseName' => $fileBaseName,
                    'newImagePath' => $newImagePath,
                    'modelImage' => $dir . '/' . date('FY') . '/' . $fileBaseName,
                ];
            }
        }

        if ($savedImages) {
            $modelImages = [];
            foreach ($savedImages as $savedImage) {
                $modelImages[] = $savedImage['modelImage'];
                $newImageDir = $savedImage['newImageDir'];
                $fileBaseName = $savedImage['fileBaseName'];
                $newImagePath = $savedImage['newImagePath'];
                if ($thumbs) {
                    $image = Image::make($newImagePath);
                    $image->backup();
                    if ($image) {
                        $ext = mb_strrchr($fileBaseName, '.');
                        $pos = mb_strrpos($fileBaseName, '.');
                        $fileName = mb_substr($fileBaseName, 0, $pos);
                        foreach ($thumbs as $key => $value) {
                            $image->fit($value[0], $value[1])->save($newImageDir . $fileName . '-' . $key . $ext);
                            $image->reset();
                        }
                    }
                }
            }
            $model->images = json_encode($modelImages);
            $model->save();
        }
        return $model;
    }

    public static function createModelImageThumbs($imagePath, $model, $dir, $thumbs)
    {
    }

    public static function checkModelActive($model)
    {
        $className = get_class($model);
        if (!isset($model->status) || !defined("$className::STATUS_ACTIVE") || !defined("$className::STATUS_SOON") || ((int)$model->status !== $className::STATUS_ACTIVE && (int)$model->status !== $className::STATUS_SOON)) {
            abort(404);
        }

    }

    public static function staticText($key, $cacheTime = 21600)
    {
        $locale = app()->getLocale();
        return Cache::remember($key, $cacheTime, function () use ($key, $locale) {
            return StaticText::where('key', $key)->withTranslation($locale)->first();
        });
    }

    public static function setting($key, $cacheTime = 3600)
    {
        return Cache::remember($key, $cacheTime, function () use ($key) {
            return setting($key);
        });
    }

    public static function seoTemplate($model, $name, $replacements = [])
    {
        $texts = [
            'seo_template_' . $name . '_seo_title',
            'seo_template_' . $name . '_meta_description',
            'seo_template_' . $name . '_meta_keywords',
            'seo_template_' . $name . '_description',
            'seo_template_' . $name . '_body',
        ];
        foreach ($texts as $text) {
            $currentProperty = str_replace('seo_template_' . $name . '_', '', $text);
            if (empty($model->$currentProperty)) {
                $template = self::staticText($text);
                if ($template) {
                    $model->$currentProperty = self::replaceTemplates($template->description, $replacements);
                }
            }
        }
        return $model;
    }

    public static function replaceTemplates($text, $replacements = [])
    {
        if (!$replacements) {
            return $text;
        }
        return str_replace(
            array_map(
                function ($value) {
                    return '{' . $value . '}';
                },
                array_keys($replacements)
            ),
            array_values($replacements),
            $text
        );
    }

    public static function addInitialReview($model)
    {
        $data = [
            'name' => 'Админ',
            'body' => 'Отлично!',
            'rating' => 5,
            'status' => Review::STATUS_ACTIVE,
        ];
        $model->reviews()->create($data);
    }

    public static function sendSMS($messageId, $phoneNumber, $message)
    {
        if (config('app.env') != 'production') {
            Log::info($message);
            return true;
        }

        // return self::sendSMSDiArt($messageId, $phoneNumber, $message);
        return self::sendSMSPlayMobile($messageId, $phoneNumber, $message);
        // return self::sendSMSEskiz($messageId, $phoneNumber, $message);
        // return true;
    }

    public static function sendSMSDiArt($messageId, $phoneNumber, $message)
    {
        $phoneNumber = preg_replace('#[^\d]#', '', $phoneNumber);
        $data = [
            ['phone' => $phoneNumber, 'text' => $message],
            // Если сообщения приходят в неправильной кодировке, используйте iconv:
            //['phone' => 'NUMBER', 'text' => utf8_encode(iconv('windows-1251', 'utf-8', 'TEXT'))],
        ];

        $client = new Client();
        $query = [
            "login" => config('services.diart.login'),
            "password" => config('services.diart.password'),
            "data" => json_encode($data)
        ];
        try {
            $client->request('POST', config('services.diart.api_url'), [
                'form_params' => $query
            ]);
        } catch (RequestException $e) {
            Log::info($e->getMessage());
        }
        return true;
    }

    public static function sendSMSPlayMobile($messageId, $phoneNumber, $message)
    {
        try {
            $client = new Client([
                'base_uri' => config('services.play_mobile.api_url'),
                'timeout'  => 5.0,
            ]);
            $response = $client->request('POST', 'send', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(config('services.play_mobile.login') . ':' . config('services.play_mobile.password')),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messages' => [
                        [
                            'recipient' => $phoneNumber,
                            'message-id' => $messageId,
                            'sms' => [
                                'originator' => 'AllGoodUZ',
                                'content' => [
                                    'text' => $message,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $code = $response->getStatusCode(); // 200
            $reason = $response->getReasonPhrase(); // OK
            return $code == 200 ? true : false;

        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }

        return false;
    }

    public static function sendSMSEskiz($messageId, $phoneNumber, $message)
    {
        // try {
        //     $client = new Client([
        //         'base_uri' => config('services.play_mobile.api_url'),
        //         'timeout'  => 5.0,
        //     ]);
        //     $response = $client->request('POST', 'send', [
        //         'headers' => [
        //             'Authorization' => 'Basic ' . base64_encode(config('services.play_mobile.login') . ':' . config('services.play_mobile.password')),
        //             'Accept' => 'application/json',
        //             'Content-Type' => 'application/json',
        //         ],
        //         'json' => [
        //             'messages' => [
        //                 [
        //                     'recipient' => $phoneNumber,
        //                     'message-id' => $messageId,
        //                     'sms' => [
        //                         'originator' => '3700',
        //                         'content' => [
        //                             'text' => $message,
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ],
        //     ]);

        //     $code = $response->getStatusCode(); // 200
        //     $reason = $response->getReasonPhrase(); // OK
        //     return $code == 200 ? true : false;

        // } catch (\Throwable $e) {
        //     Log::info($e->getMessage());
        // }

        return false;
    }

    public static function messagePrefix()
    {
        $name = str_replace(' ', '', Str::lower(config('app.name')));
        $prefix = config('app.env') == 'production' ? $name : 'test' . $name;
        return $prefix;
    }

    public static function getTree($collection, $parent = null, $level = 1)
    {
        $filtered = $collection->filter(function ($value) use ($parent) {
            return $value['parent_id'] == $parent;
        });
        $filtered->map(function ($item) use ($collection, $level) {
            $item['children'] = self::getTree($collection, $item->id, $level + 1);
        });
        return $filtered;
    }

    public static function activeCategories($category, $ids = [])
    {
        $ids[] = $category->id;
        if ($category->parent) {
            $ids = self::activeCategories($category->parent, $ids);
        }
        return $ids;
    }

    public static function siblingPages(Page $page)
    {
        if ($page->parent_id) {
            $siblingPages = Page::active()->where('parent_id', $page->parent_id)->with(['pages' => function ($q) {
                $q->active()->withTranslation(app()->getLocale());
            }])->get();
        } else {
            $siblingPages = Page::active()->whereNull('parent_id')->with(['pages' => function ($q) {
                $q->active()->withTranslation(app()->getLocale());
            }])->get();
        }
        if (!$siblingPages->isEmpty()) {
            $siblingPages = $siblingPages->translate();
        }
        return $siblingPages;
    }

    /**
     * Get file url
     */
    public static function getFileUrl($fileString)
    {
        $file = json_decode($fileString);
        return (!empty($file[0]->download_link)) ? Voyager::image($file[0]->download_link) : '';
        // return (!empty($file[0]->download_link)) ? Storage::disk(config('voyager.storage.disk'))->url($file[0]->download_link) : '';
    }

    /**
     * Get file original name
     */
    public static function getFileOriginalName($fileString)
    {
        $file = json_decode($fileString);
        return (!empty($file[0]->original_name)) ? $file[0]->original_name : '';
    }

    public static function getCurrentRegionID()
    {
        $currentRegionID = Cookie::get('region_id', '');
        if (!$currentRegionID || !is_numeric($currentRegionID)) {
            return 14; // Tashkent ID
        }
        return (int)$currentRegionID;
    }

    public static function getCurrentRegion()
    {
        $currentRegionID = self::getCurrentRegionID();
        $region = Region::find($currentRegionID);
        if (!$region) {
            $region = Region::first();
        }
        $region = self::translation($region);
        return $region;
    }

    public static function changeEnvironmentVariable($key, $value)
    {
        $path = base_path('.env');

        if (is_bool(env($key))) {
            $old = env($key) ? 'true' : 'false';
        } elseif (env($key) === null) {
            $old = 'null';
        } else {
            $old = env($key);
        }

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                "$key=" . $old,
                "$key=" . $value,
                file_get_contents($path)
            ));
        }
    }

    public static function exchangeRate()
    {
        // $rate = (float)self::setting('site.exchange_rate');
        // if ($rate <= 0) {
        //     $rate = 1;
        // }
        // return $rate;
        return 1;
    }

    public static function escapeFullTextSearch($string)
    {
        $string = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $string);
        $string = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $string);
        return $string;
    }

    public static function fullTextSearchPrepare($string) {
        $string = static::escapeFullTextSearch($string);
        $words = [];
        $wordsRaw = explode(' ', $string);
        foreach ($wordsRaw as $word) {
            $word = trim($word);
            $words[] = $word;
        }
        return implode(' ', $words);
    }

    public static function fullTextSearchPrepareStrict($string) {
        $string = static::escapeFullTextSearch($string);
        $words = [];
        $wordsRaw = explode(' ', $string);
        foreach ($wordsRaw as $word) {
            $word = trim($word);
            if (mb_strlen($word) < 3) {
                continue;
            }
            $words[] = '+' . $word;
        }
        return implode(' ', $words);
    }

    public static function partnersPrices($basePrice)
    {
        $prices = [];
        $partners = Cache::remember('partners', 300, function () {
            return Partner::active()->orderBy('order')->withTranslations()->with(['partnerInstallments' => function($q) {
                $q->active()->orderBy('order');
            }])->get();
        });
        foreach ($partners as $partner) {
            $prices[$partner->id] = [
                'partner' => $partner,
                'prices' => [],
            ];
            foreach ($partner->partnerInstallments as $partnerInstallment) {
                $prices[$partner->id]['prices'][] = self::partnerInstallmentCalculate($partnerInstallment, $basePrice);
            }
        }
        return $prices;
    }

    public static function partnerInstallmentPricePerMonth($partnerInstallmentID, $basePrice)
    {
        $partnerInstallment = PartnerInstallment::findOrFail($partnerInstallmentID);
        $partnerInstallmentPriceItem = self::partnerInstallmentCalculate($partnerInstallment, $basePrice);
        return $partnerInstallmentPriceItem['price_per_month'];
    }

    public static function partnerInstallmentDuration($partnerInstallmentID)
    {
        $partnerInstallment = PartnerInstallment::findOrFail($partnerInstallmentID);
        $duration = (int)$partnerInstallment->duration;
        if ($duration < 1) {
            $duration = 1;
        }
        return $duration;
    }

    public static function isInstallmentPaymentMethod($paymentMethodID)
    {
        $paymentMethods = Helper::paymentMethods();
        $paymentMethod = $paymentMethods->where('id', $paymentMethodID)->first();
        return ($paymentMethod && (bool)$paymentMethod->installment);
    }

    private static function partnerInstallmentCalculate($partnerInstallment, $basePrice)
    {
        $percent = (float)$partnerInstallment->percent;
        if ($percent < 0) {
            $percent = 0;
        }
        $duration = (int)$partnerInstallment->duration;
        if ($duration < 1) {
            $duration = 1;
        }
        $partnerInstallmentPrice = round($basePrice * (1 + $percent / 100));
        $partnerInstallmentPricePerMonth = round($partnerInstallmentPrice / $duration);
        return [
            'partner_installment' => $partnerInstallment,
            'price' => $partnerInstallmentPrice,
            'price_formatted' => Helper::formatPrice($partnerInstallmentPrice),
            'price_per_month' => $partnerInstallmentPricePerMonth,
            'price_per_month_formatted' => Helper::formatPrice($partnerInstallmentPricePerMonth),
            'duration' => $duration,
        ];
    }

    public static function trendyolUpdateStock(array $barcodes)
    {
        $trendyol = new TrendyolService();
        $res = $trendyol->stock($barcodes);
        if ($res && $res->getStatusCode() == 200) {
            $json = json_decode($res->getBody()->getContents());
            if ($json && is_array($json)) {
                foreach ($json as $row) {
                    Product::where('barcode', $row->barcode)->update([
                        'in_stock' => $row->availableQuantity,
                    ]);
                }
            }
        }
    }

    public static function trendyolPurchase(Order $order, array $items)
    {
        $trendyol = new TrendyolService();
        $res = $trendyol->purchase($items);
        if ($res && ($res->getStatusCode() == 200 || $res->getStatusCode() == 201)) {
            $json = json_decode($res->getBody()->getContents());
            if ($json && !empty($json->requestNumber)) {
                $order->trendyol_request_number = $json->requestNumber;
                $order->save();
            }
        }
    }

    public static function getDeviceName()
    {
        $deviceName = trim (Browser::platformName() . ' ' . Browser::browserName() . ' ' . Browser::deviceModel());
        if ($deviceName) {
            $deviceName = uniqid();
        }
        return $deviceName;
    }

    public static function phoneNumberRegex()
    {
        return '^998\d{9}$';
    }

    public static function checkOTP(User $user, $code)
    {
        $result = [
            'success' => true,
        ];
        $otp = $user->otps()->latest()->first();
        if (!$otp) {
            $result['success'] = false;
            $result['error'] = __('OTP has not been sent');
        } elseif (!Hash::check($code, $otp->content)) {
            $result['success'] = false;
            $result['error'] = __('Invalid OTP');
        }
        return $result;
    }

    public static function checkOTPByPhoneNumber($phoneNumber, $code)
    {
        $result = [
            'success' => true,
        ];
        $otp = Otp::where('phone_number', $phoneNumber)->latest()->first();
        if (!$otp) {
            $result['success'] = false;
            $result['error'] = __('OTP has not been sent');
        } elseif (!Hash::check($code, $otp->content)) {
            $result['success'] = false;
            $result['error'] = __('Invalid OTP');
        }
        return $result;
    }

    /**
     * Update Env file
     * @param array $data
     */
    public static function updateEnv(array $data)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $contents = file_get_contents($path);
            foreach ($data as $key => $value) {
                $rowWithQuotes = $key . '="' . env($key, '') . '"';
                $rowWithoutQuotes = $key . '=' . env($key, '');
                if (strpos($contents, $rowWithQuotes) !== false) {
                    $contents = str_replace($rowWithQuotes, $key . '="' . $value . '"', $contents);
                } elseif(strpos($contents, $rowWithoutQuotes) !== false) {
                    $contents = str_replace($rowWithoutQuotes, $key . '=' . $value, $contents);
                } elseif(preg_match('#' . $key . '\=.*#', $contents)) {
                    $contents = preg_replace('#' . $key . '\=.*#', $key . '="' . $value . '"', $contents);
                }
            }
            file_put_contents($path, $contents);
        }
    }

    public static function paymentMethods()
    {
        $locale = app()->getLocale();
        return Cache::remember('payment-methods-' . $locale, 300, function () use ($locale) {
            return PaymentMethod::active()->withTranslation($locale)->orderBy('order')->get();
        });
    }

    public static function paymentMethodsDesktop()
    {
        $locale = app()->getLocale();
        return Cache::remember('payment-methods-desktop-' . $locale, 300, function () use ($locale) {
            return PaymentMethod::active()->withTranslation($locale)->orderBy('order')->where('desktop', 1)->get();
        });
    }

    public static function paymentMethodsApp()
    {
        $locale = app()->getLocale();
        return Cache::remember('payment-methods-app-' . $locale, 300, function () use ($locale) {
            return PaymentMethod::active()->withTranslation($locale)->orderBy('order')->where('mobile', 1)->get();
        });
    }

    public static function paymentMethodsIds()
    {
        return Helper::paymentMethods()->pluck('id')->toArray();
    }

    public static function paymentMethodTitle($payment_method_id)
    {
        $paymentMethod = static::paymentMethods()->where('id', $payment_method_id)->first();
        return $paymentMethod ? $paymentMethod->getTranslatedAttribute('name') : '';
    }

    public static function payWithAtmos(Order $order, Card $card)
    {
        if (!auth()->check() || auth()->user()->id != $card->user_id) {
            return false;
        }
        $locale = app()->getLocale();
        $service = new AtmosService();
        $storeId = config('services.atmos.store_id');

        // create transaction
        $sendData = [
            'amount' => $order->total_tiyin,
            'account' => 'order-id-' . $order->id,
            'store_id' => $storeId,
            'lang' => $locale,
        ];

        $response = $service->merchantPayCreate($sendData);
        if (!$response) {
            return false;
        }
        $json = json_decode($response->getBody()->getContents());
        if (empty($json->result->code) || $json->result->code != 'OK') {
            return false;
        }
        $atmosTransaction = $order->atmosTransactions()->create([
            'transaction_id' => $json->transaction_id,
        ]);

        // pre confirm transaction
        $sendData = [
            'card_token' => $card->atmos_card_token,
            'store_id' => $storeId,
            'transaction_id' => $atmosTransaction->transaction_id,
        ];
        $response = $service->merchantPayPreConfirm($sendData);
        if (!$response) {
            return false;
        }
        $json = json_decode($response->getBody()->getContents());
        if (empty($json->result->code) || $json->result->code != 'OK') {
            return false;
        }

        // confirm transaction
        $sendData = [
            'transaction_id' => $atmosTransaction->transaction_id,
            // 'otp' => '',
            'store_id' => $storeId,
        ];
        $response = $service->merchantPayConfirm($sendData);
        if (!$response) {
            return false;
        }
        $json = json_decode($response->getBody()->getContents());
        if (empty($json->result->code) || $json->result->code != 'OK') {
            return false;
        }

        // update transaction
        $atmosTransaction->update([
            'success_trans_id' => $json->store_transaction->success_trans_id ?? '',
            'terminal_id' => $json->store_transaction->terminal_id ?? '',
            'prepay_time' => $json->store_transaction->prepay_time ?? '',
            'confirm_time' => $json->store_transaction->confirm_time ?? '',
            'card_id' => $json->store_transaction->card_id ?? '',
            'status_code' => $json->store_transaction->status_code ?? '',
            'status_message' => $json->store_transaction->status_message ?? '',
        ]);

        // set order paid
        $order->setPaid();

        return true;
    }
}
