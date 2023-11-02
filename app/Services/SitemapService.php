<?php

namespace App\Services;

use App\Brand;
use App\Category;
use App\Page;
use App\Product;
use App\Publication;
use DateTime;
use Illuminate\Support\Carbon;
use Throwable;

class SitemapService
{
    public function create()
    {
        $locales = array_keys(config('laravellocalization.supportedLocales'));
        $sitemapsDir = $this->getSitemapsDir();
        $sitemapsCounter = 0;

        // delete old files
        $oldFiles = ['sitemap.xml'];
        $oldFilesQty = 100;
        for ($i = 1; $i <= 100; $i++) {
            $oldFiles[] = 'sitemap' . $i . '.xml';
        }
        foreach ($oldFiles as $oldFile) {
            $file = public_path($oldFile);
            if(file_exists($file)) {
                unlink($file);
            }
        }

        // generate all models except products
        $files = [];
        $all = [];
        $data = [
            'pages' => ['items' => Page::active()->withTranslations($locales)->get(), 'priority' => 0.9, 'changeFrequency' => 'weekly', ],
            'categories' => ['items' => Category::active()->withTranslations($locales)->get(), 'priority' => 0.8, 'changeFrequency' => 'weekly', ],
            // 'products' => ['items' => Product::active()->withTranslations($locales)->get(), 'priority' => 0.7, 'changeFrequency' => 'weekly', ],
            'publications' => ['items' => Publication::active()->withTranslations($locales)->get(), 'priority' => 0.7, 'changeFrequency' => 'weekly', ],
            'brands' => ['items' => Brand::active()->withTranslations($locales)->get(), 'priority' => 0.7, 'changeFrequency' => 'weekly', ],
        ];
        foreach ($data as $type => $content) {
            if ($content['items']->isEmpty()) {
                continue;
            }
            foreach ($content['items'] as $item) {
                foreach($locales as $locale) {
                    $all[] = [
                        'url' => $item->getURL($locale),
                        'priority' => $content['priority'],
                        'lastModificationDate' => $item->updated_at->format(DateTime::ATOM) ?? date('Y-m-d'),
                        'changeFrequency' => $content['changeFrequency'],
                    ];
                }
            }
        }

        $all = array_chunk($all, 9000);
        foreach ($all as $urls) {
            $files[] = view('sitemap', compact('urls'))->render();
        }

        // write new sitemap files
        foreach ($files as $value) {
            $sitemapsCounter++;
            $fileName = $sitemapsDir . '/' . 'sitemap' . $sitemapsCounter . '.xml';
            file_put_contents($fileName, $value);
        }


        // generate products
        $sitemapsCounter++;
        $productsCounter = 0;
        $products = Product::active()->cursor();
        $fileName = $sitemapsDir . '/' . 'sitemap' . $sitemapsCounter . '.xml';
        $fp = fopen($fileName, 'w');
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
        foreach ($products as $product) {
            $productsCounter++;
            foreach($locales as $locale) {
                fwrite($fp, '<url><loc>' . $product->getURL($locale) . '</loc><lastmod>' . ($product->updated_at->format(DateTime::ATOM) ?? date('Y-m-d')) . '</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url>');
            }
            if ($productsCounter >= 9000) {
                fclose($fp);
                $sitemapsCounter++;
                $fileName = $sitemapsDir . '/' . 'sitemap' . $sitemapsCounter . '.xml';
                $fp = fopen($fileName, 'w');
                fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
                $productsCounter = 0;
            }
        }
        fwrite($fp, '</urlset>');
        fclose($fp);

        // rewrite sitemap index file
        $getSitemapsBaseURL = $this->getSitemapsBaseURL();
        $filesQuantity = $sitemapsCounter;
        $sitemapLastmod = (Carbon::now())->format(DateTime::ATOM);
        $sitemapIndexContent = view('sitemapindex', compact('filesQuantity', 'sitemapLastmod', 'getSitemapsBaseURL'))->render();
        file_put_contents($sitemapsDir . '/sitemapindex.xml', $sitemapIndexContent);
    }

    public function getSitemapsDir()
    {
        $sitemapsDir = base_path('../public_html/sitemaps');
        if (!is_dir($sitemapsDir)) {
            $sitemapsDir = public_path() . '/sitemaps';
        }
        return $sitemapsDir;
    }

    public function getSitemapsBaseURL()
    {
        return route('home') . '/sitemaps';
    }
}
