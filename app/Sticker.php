<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class Sticker extends Model
{
    use HasFactory;
    use Resizable;
    use Translatable;

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

    public static $imgSizes = [
        'micro' => [35, 35],
        'small' => [100, 100],
        // 'medium' => [400, 400],
    ];

    protected $translatable = ['name', 'description'];

    protected $appends = ['img'];

    protected $guarded = [];

    /**
     * scope active
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Get main image
     */
    public function getImgAttribute()
    {
        return $this->image ? Voyager::image($this->image) : asset('images/no-image.jpg');
    }

    /**
     * Get micro image
     */
    public function getMicroImgAttribute()
    {
        return $this->image ? Voyager::image($this->getThumbnail($this->image, 'micro')) : asset('images/no-image.jpg');
    }

    /**
     * Get small image
     */
    public function getSmallImgAttribute()
    {
        return $this->image ? Voyager::image($this->getThumbnail($this->image, 'small')) : asset('images/no-image.jpg');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
