<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekProducts extends Model
{
    use HasFactory;

    protected $table = 'week_products';

    protected $guarded = [];
}
