<?php

namespace App\Http\Controllers\Api;

use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends ApiController
{
    protected $select;
    protected $locales;

    public function __construct()
    {
        $this->select = ['id', 'name', 'slug', 'image'];
        $this->locales = array_keys(config('laravellocalization.supportedLocales'));
    }

    public function index(Request $request)
    {
        $brands = Brand::select($this->select)->active()->withTranslations($this->locales)->get();
        foreach ($brands as $key => $brand) {
            $brands[$key] = $this->formatBrand($brand);
        }
        return $brands->toArray();
    }

    public function show(Request $request, Brand $brand)
    {
        $brand = Brand::select($this->select)
            ->withTranslations($this->locales)
            ->where('brands.id', $brand->id)
            ->first();

        $brand = $this->formatBrand($brand);
        return $brand;
    }

    private function formatBrand($brand)
    {
        $appURL = config('app.url');
        $translations = [];
        foreach ($this->locales as $locale) {
            $translations[$locale] = [];
            foreach ($this->select as $selectValue) {
                $translation = $brand['translations']->where('locale', $locale)->where('column_name', $selectValue)->first();
                if ($translation) {
                    $translations[$locale][$selectValue] = $translation->value;
                }
            }
            if (empty($translations[$locale])) {
                unset($translations[$locale]);
            }
        }
        $brand['languages'] = $translations;

        $brand = $brand->toArray();
        $brand['img'] = $appURL . $brand['img'];
        unset($brand['translations']);
        unset($brand['image']);

        return $brand;
    }
}
