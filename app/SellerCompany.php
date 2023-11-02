<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class SellerCompany extends Model
{
    use HasApiTokens, HasFactory;

    protected $guarded = [];
}
