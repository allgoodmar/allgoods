<?php

namespace App\Listeners;

use App\Interfaces\ModelSavedInterface;
use App\Jobs\ProcessProduct;
use App\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateProductMinPricePerMonth
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
     * @param  ModelSavedInterface  $event
     * @return void
     */
    public function handle(ModelSavedInterface $event)
    {
        // $product = $event->getModel();
        // ProcessProduct::dispatch($product)->delay(now()->addSeconds(10));
        // $installmentPlans = $product->installmentPlans;
        // if (!$installmentPlans->isEmpty()) {
        //     $maxInstallmentPlanMonths = $installmentPlans->sortByDesc('months')->first()->months;
        //     $product->min_price_per_month = $product->current_price / $maxInstallmentPlanMonths;
        //     Product::withoutEvents(function () use ($product) {
        //         $product->save();
        //     });
        // }
    }
}
