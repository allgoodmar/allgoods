<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckProductGroupAttributeValues
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $product = $event->getModel();
        $productGroup = $product->productGroup;
        if (!$productGroup) {
            return;
        }

        $productGroupAttributeIDs = $productGroup->attributes->pluck('id');
        $checkProductAttributeIDs = $product->attributes()->whereIn('attributes.id', $productGroupAttributeIDs)->get();
        if ($productGroupAttributeIDs->count() != $checkProductAttributeIDs->count()) {
            $product->productGroup()->dissociate();
            $product->saveQuietly();
        }

        $productGroup->syncAttributeValues();
    }
}
