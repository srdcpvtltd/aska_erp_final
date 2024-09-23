<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuttingOrder extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'farming_detail_id',
        'created_by',
    ];
}
