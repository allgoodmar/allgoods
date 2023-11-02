<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class ShippingMethod extends Model
{
    use Resizable;
    use Translatable;

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

    protected $translatable = ['name', 'description'];

    protected $guarded = [];

    /**
     * scope active
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
