<?php

namespace App\Listeners;

use App\Interfaces\ModelSavedInterface;
use App\Product;
use App\Search;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GenerateModelSearchText
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ModelSavedInterface $event
     * @return void
     */
    public function handle(ModelSavedInterface $event)
    {
        $model = $event->getModel();
        $model->searches()->delete();

        $baseClassName = class_basename(get_class($model->getModel()));

        if (isset($model->status) && $model->status != 1) {
            return;
        }

        if ($baseClassName == 'Product') {
            $searchBody = $this->productSearchBody($model);
        } else {
            $searchBody = '';
            if (!empty($model->name)) {
                $searchBody .= $model->name . PHP_EOL;
            }
            if (!empty($model->description)) {
                $searchBody .= $model->description . PHP_EOL;
            }
            if (!empty($model->body)) {
                $searchBody .= strip_tags($model->body) . PHP_EOL;
            }
            if (!empty($model->first_name)) {
                $searchBody .= $model->first_name . PHP_EOL;
            }
            if (!empty($model->last_name)) {
                $searchBody .= $model->last_name . PHP_EOL;
            }
            if (!empty($model->middle_name)) {
                $searchBody .= $model->middle_name . PHP_EOL;
            }
            if (!empty($model->specifications)) {
                $searchBody .= strip_tags($model->specifications) . PHP_EOL;
            }

            $currentLocale = app()->getLocale();

            foreach (config('laravellocalization.supportedLocales') as $key => $value) {
                if ($key != $currentLocale) {
                    $model = $model->translate($key);
                    if ($model->name) {
                        $searchBody .= $model->name . PHP_EOL;
                    }
                    if ($model->description) {
                        $searchBody .= $model->description . PHP_EOL;
                    }
                    if ($model->body) {
                        $searchBody .= strip_tags($model->body) . PHP_EOL;
                    }
                    if ($model->first_name) {
                        $searchBody .= $model->first_name . PHP_EOL;
                    }
                    if ($model->last_name) {
                        $searchBody .= $model->last_name . PHP_EOL;
                    }
                    if ($model->middle_name) {
                        $searchBody .= $model->middle_name . PHP_EOL;
                    }
                    if ($model->specifications) {
                        $searchBody .= $model->middle_name . PHP_EOL;
                    }
                }
            }
        }

        $search = new Search();
        $search->body = $searchBody;

        $model->getModel()->searches()->save($search);
    }

    private function productSearchBody($model)
    {
        $searchBody = '';
        $brand = $model->brand;
        $categories = $model->categories;
        $searchBody .= ($model->name ?? '') . PHP_EOL;
        $searchBody .= ($model->description ?? '') . PHP_EOL;
        $searchBody .= ($brand->name ?? '') . PHP_EOL;
        foreach ($categories as $category) {
            $searchBody .= $category->name . PHP_EOL;
        }

        $currentLocale = app()->getLocale();

        foreach (config('laravellocalization.supportedLocales') as $key => $value) {
            if ($key != $currentLocale) {
                $model = $model->translate($key);
                $searchBody .= $model->name . PHP_EOL;
                $searchBody .= $model->description . PHP_EOL;

                if ($brand) {
                    $brandTranslated = $brand->translate($key);
                    $searchBody .= ($brandTranslated->name ?? '') . PHP_EOL;
                }
                foreach ($categories as $category) {
                    $categoryTranslated = $category->translate($key);
                    $searchBody .= $categoryTranslated->name . PHP_EOL;
                }
            }
        }
        return $searchBody;
    }
}
