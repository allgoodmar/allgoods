<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class PaymentMethod extends Model
{
    use HasFactory;
    use Resizable;
    use Translatable;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $imgSizes = [
        'micro' => [50, 50],
    ];

    protected $appends = [
        'img',
    ];

    protected $translatable = ['name', 'description'];

    protected $guarded = [];

    public function getImgAttribute()
    {
        return $this->image ? Voyager::image($this->image) : asset('images/no-product-image.jpg');
    }

    public function getMicroImgAttribute()
    {
        return $this->image ? Voyager::image($this->getThumbnail($this->image, 'micro')) : asset('images/no-product-image.jpg');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
