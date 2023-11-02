<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class StaticText extends Model
{
    use Resizable;
    use Translatable;
    use HasFactory;

    protected $guarded = [];

    protected $translatable = ['name', 'description'];

    /**
     * Get original image
     */
    public function getImgAttribute()
    {
        return Voyager::image($this->image);
    }
}
