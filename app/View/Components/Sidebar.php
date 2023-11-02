<?php

namespace App\View\Components;

use App\Page;
use Illuminate\View\Component;

class Sidebar extends Component
{

    public $page;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($page = null)
    {
        $this->page = $page;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $pageInstallments = Page::find(8)->translate();
        return view('components.sidebar', compact('pageInstallments'));
    }
}
