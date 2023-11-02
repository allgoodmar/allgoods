<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportPartnerMargin extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function importPartner()
    {
        return $this->belongsTo(ImportPartner::class);
    }
}
