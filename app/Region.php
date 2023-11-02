<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Region extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $translatable = ['name', 'short_name'];

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
}
