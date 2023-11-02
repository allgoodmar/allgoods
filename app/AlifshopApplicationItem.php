<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlifshopApplicationItem extends Model
{
    protected $guarded = [];

    public function alifshopApplication()
    {
        return $this->belongsTo(AlifshopApplication::class);
    }
}
