<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	const STATUS_NEW = 0;
	const STATUS_READ = 1;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }
}
