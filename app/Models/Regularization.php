<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Regularization extends Model
{
    use HasFactory;

    protected $table = 'regularizations';
    protected $fillable = [
        'user_id',
        'company_id',
        'regularization_date',
        'check_in_at',
        'check_out_at',
        'reason',
        'check_in_latitude',
        'check_out_latitude',
        'check_in_longitude',
        'check_out_longitude',
        'note',
        'edit_remark',
        'regularization_status',
        'created_by',
        'updated_by'
        // 'check_in_type',
        // 'check_out_type',
        // 'worked_hour',
        // 'overtime',
        // 'undertime',
    ];

    const RECORDS_PER_PAGE = 20;

    const ATTENDANCE_PENDING = 0;
    const ATTENDANCE_APPROVED = 1;
    const ATTENDANCE_REJECTED = 2;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::user()->id;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->id;
        });
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}