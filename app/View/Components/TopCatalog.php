<?php

namespace App\View\Components;

use App\Helpers\Helper;
use Illuminate\View\Component;

class TopCatalog extends Component
{
    public $isOpen = 0;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($isOpen)
    {
        $this->isOpen = $isOpen;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $categories = Helper::categories('menu');

        return view('components.top_catalog', compact('categories'));
    }
}
