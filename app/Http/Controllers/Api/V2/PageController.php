<?php

namespace App\Http\Controllers\Api\V2;

use App\Page;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $pages = Page::active()->withTranslation($locale)->get();
        return PageResource::collection($pages);
    }

    public function show(Request $request, Page $page)
    {
        // $locale = app()->getLocale();
        $page->load('translations');
        return new PageResource($page);
    }
}
