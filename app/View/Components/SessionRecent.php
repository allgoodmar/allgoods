<?php

namespace App\View\Components;

use App\Product;
use Illuminate\View\Component;

class SessionRecent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $locale = app()->getLocale();
        $current_session = session()->getId();
        $bestsellerProductsQuery = Product::select("products.*")
            ->active()
            ->where('session_recent_products.sessionId', $current_session)
            ->join('session_recent_products', 'session_recent_products.product_id', '=', 'products.id')
            ->withTranslation($locale)
            ->orderBy('products.created_at');
        $sessionRecentProducts = $bestsellerProductsQuery->take(6)->get();

        if (!$sessionRecentProducts->isEmpty()) {
            $sessionRecentProducts = $sessionRecentProducts->translate();
        }

        return view('components.session-recent', compact('sessionRecentProducts'));
    }
}
