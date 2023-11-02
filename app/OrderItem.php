<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function importPartner()
    {
        return $this->belongsTo(ImportPartner::class);
    }

    public function isTrendyolProduct()
    {
        return $this->import_partner_id == 3;
    }

    public function getImportPartnerNameAttribute()
    {
        return $this->importPartner->name ?? '';
    }
}
