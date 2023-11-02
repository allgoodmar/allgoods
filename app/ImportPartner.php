<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportPartner extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function importPartnerMargins()
    {
        return $this->hasMany(ImportPartnerMargin::class);
    }
}
