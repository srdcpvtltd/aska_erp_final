<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmingPayment extends Model
{
    use HasFactory;

    const SECURITY_DEPOSIT = 'Security Deposit';
    const BANK_GUARANTEE = 'Bank Guarantee';
    const REIMBURSEMENT = 'Reimbursement';
    const Seed_Stock_Entry = 'Seed Stock Entry';

    protected $fillable = [
        'farming_id',
        'registration_number',
        'agreement_number',
        'date',
        'amount',
        'type',
        'created_by',
        'bank',
        'loan_account_number',
        'ifsc',
        'branch',
        'receipt_type'
    ];

    public function farming()
    {
        return $this->belongsTo(Farming::class,'farming_id');
    }
}