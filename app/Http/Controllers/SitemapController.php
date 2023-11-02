<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $service = new SitemapService();
        // $service->create();
        $sitemapsDir = $service->getSitemapsDir();

        return response(file_get_contents($sitemapsDir . '/sitemapindex.xml'))
            ->withHeaders([
                'Content-Type' => 'text/xml'
            ]);
    }
}
