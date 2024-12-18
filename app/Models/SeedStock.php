<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeedStock extends Model
{
    use HasFactory;

    public function farmer(){
        return $this->belongsTo(Farming::class, 'farmer_id');
    }   
     
    public function product(){
        return $this->belongsTo(ProductService::class,'product_id');
    }
}
