<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class Poll extends Model
{
    use Translatable;
    use Resizable;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $translatable = ['question'];

    protected $guarded = [];

    public function pollAnswers()
    {
        return $this->hasMany(PollAnswer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    public function getURLAttribute()
    {
        return LaravelLocalization::localizeURL('polls/' . $this->id);
    }
}
