<?php

namespace App\View\Components;

use App\Helpers\Helper;
use Illuminate\View\Component;

class Myslide extends Component
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
        $slides = Helper::banners('slide_big');
        if ($slides) {
            $slides = $slides->translate();
        }

        $slides_mini = Helper::banners('slide_big_mini');
        if ($slides_mini) {
            $slides_mini = $slides_mini->translate();
        }

        $slides_medium_left = Helper::banners('slide_medium_1');
        if ($slides_medium_left) {
            $slides_medium_left = $slides_medium_left->translate();
        }

        $slides_medium_right = Helper::banners('slide_medium_2');
        if ($slides_medium_right) {
            $slides_medium_right = $slides_medium_right->translate();
        }

        $slides_small_1 = Helper::banners('slide_small_1');
        if ($slides_small_1) {
            $slides_small_1 = $slides_small_1->translate();
        }

        $slides_small_2 = Helper::banners('slide_small_2');
        if ($slides_small_2) {
            $slides_small_2 = $slides_small_2->translate();
        }

        $slides_small_3 = Helper::banners('slide_small_3');
        if ($slides_small_3) {
            $slides_small_3 = $slides_small_3->translate();
        }

        $slides_small_4 = Helper::banners('slide_small_4');
        if ($slides_small_4) {
            $slides_small_4 = $slides_small_4->translate();
        }

        $compact = compact('slides', 'slides_mini', 'slides_medium_left', 'slides_medium_right', 'slides_small_1', 'slides_small_2', 'slides_small_3', 'slides_small_4');

        return view('components.myslide', $compact);
    }
}
