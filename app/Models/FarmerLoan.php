<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmerLoan extends Model
{
    use HasFactory; 

    protected $fillable = [
        'farming_id',
        'registration_number',
        'agreement_number',
        'date',
        'created_by',
        'loan_category_id'
    ];

    protected $casts = [
        'loan_type_id' => 'array',
        'price_kg' => 'array',
        'quantity' => 'array',
        'total_amount' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ProductServiceCategory::class,'loan_category_id');
    }

    public function type()
    {
        return $this->belongsTo(ProductService::class,'loan_type_id');
    }

    public function farming()
    {
        return $this->belongsTo(Farming::class,'farming_id');
    }

    public function farming_payment()
    {
        return $this->belongsTo(FarmingPayment::class,'farming_payment_id');
    }

}
