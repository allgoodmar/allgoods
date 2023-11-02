<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referal extends Model
{
    use HasFactory;

    protected $guard = [];
    
    protected $fillable = ['ref_user_id', 'name_ref', 'amount_coupon'];
}
