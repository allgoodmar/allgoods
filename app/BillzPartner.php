<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillzPartner extends Model
{
    use HasFactory;

    // Billz Partners Codes
    CONST BILLZ_ADRAS = '8888-01';
    CONST BILLZ_COKOS = '8888-02';
    CONST BILLZ_MYLIFESTYLE = '8888-03';
    CONST BILLZ_OMIODIOBRAND = '8888-04';
    CONST BILLZ_TOPAR = '8888-05';
    CONST BILLZ_DIVA = '8888-06';
    CONST BILLZ_RAYYON = '8888-07';
    CONST BILLZ_ELISIUM = '8888-08';
    CONST BILLZ_WALHALA = '8888-09';

    protected $fillable = [
        'name',
        'parent_partner',
        'code',
        'percent'
    ];
}
