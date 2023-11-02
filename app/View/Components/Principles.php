<?php

namespace App\View\Components;

use App\StaticText;
use Illuminate\View\Component;

class Principles extends Component
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
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $locale = app()->getLocale();
        $principles = StaticText::where('key', 'LIKE', 'principle_%')->orderBy('key')->withTranslation($locale)->get()->translate();
        return view('components.principles', compact('principles'));
    }
}
