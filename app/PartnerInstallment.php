<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerInstallment extends Model
{
    use HasFactory;

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

    protected $guarded = [];

    /**
     * scope active
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
