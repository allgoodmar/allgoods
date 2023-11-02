<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    protected $select;
    protected $locales;

    public function __construct()
    {
        $this->select = ['id', 'name', 'slug', 'image', 'parent_id'];
        $this->locales = array_keys(config('laravellocalization.supportedLocales'));
    }

    public function index(Request $request)
    {
        $allCategories = Category::select($this->select)->active()->withTranslations($this->locales)->get();

        $categories = $allCategories->whereNull('parent_id')->keyBy('id');

        $categories = $this->formatCategories($categories, $allCategories);

        return $categories;
    }

    public function show(Request $request, Category $category)
    {
        $locales = array_keys(config('laravellocalization.supportedLocales'));

        $childrenIds = $category->childrenIds($category);
        $allCategories = Category::select($this->select)->whereIn('id', $childrenIds)->active()->withTranslations($locales)->get();

        $categories = $allCategories->where('id', $category->id);
        $categories = $this->formatCategories($categories, $allCategories);

        return array_shift($categories);
    }

    private function formatCategories($categories, $allCategories)
    {
        $appURL = config('app.url');
        foreach ($categories as $key => $category) {

            // set translations
            $category = $this->setTranslations($category);

            // set children
            $category['children'] = $allCategories->where('parent_id', $category->id)->keyBy('id');

            // process children
            $category['children'] = $this->formatCategories($category['children'], $allCategories);

            // clean
            $category = $category->toArray();
            $category['img'] = $appURL . $category['img'];
            unset($category['image']);

            $categories[$key] = $category;
        }
        return $categories->toArray();
    }

    private function setTranslations($category)
    {
        $translations = [];
        foreach ($this->locales as $locale) {
            $translations[$locale] = [];
            foreach ($this->select as $selectValue) {
                $translation = $category['translations']->where('locale', $locale)->where('column_name', $selectValue)->first();
                if ($translation) {
                    $translations[$locale][$selectValue] = $translation->value;
                }
            }
            if (empty($translations[$locale])) {
                unset($translations[$locale]);
            }
        }
        $category['languages'] = $translations;
        unset($category['translations']);
        return $category;
    }
}
