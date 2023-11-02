<?php

namespace App\View\Components;

use App\StaticText;
use Illuminate\View\Component;

class Steps extends Component
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
        $steps = StaticText::where('key', 'LIKE', 'step_%')->orderBy('key')->withTranslation($locale)->get()->translate();
        return view('components.steps', compact('steps'));
    }
}
