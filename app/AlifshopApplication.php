<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlifshopApplication extends Model
{
    protected $guarded = [];

    public function alifshopApplicationItems()
    {
        return $this->hasMany(AlifshopApplicationItem::class);
    }
}
