<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function gram_panchyat()
    {
        return $this->belongsTo(GramPanchyat::class,'gram_panchyat_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class,'zone_id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class,'center_id');
    }
}
