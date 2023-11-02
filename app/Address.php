<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
