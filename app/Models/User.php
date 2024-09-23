<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Helpers\AttendanceHelper;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    const AVATAR_UPLOAD_PATH = 'uploads/user/avatar/';
    const RECORDS_PER_PAGE = 20;
    const GENDER = ['male', 'female', 'others'];
    const STATUS = ['pending', 'verified', 'rejected', 'suspended'];
    const EMPLOYMENT_TYPE = ['contract', 'permanent', 'temporary'];
    const USER_TYPE = ['field', 'nonField'];
    const DEVICE_TYPE = ['android', 'ios', 'web'];
    const ANDROID = 'android';
    const IOS = 'ios';
    const WEB = 'web';
    const ONLINE = 1;
    const OFFLINE = 0;
    const FIELD = 0;
    const OFFICE = 1;
    const DEMO_USERS_USERNAME = ['admin123', 'employee1'];

    const MARITAL_STATUS = [
        'single',
        'married'
    ];



    const LOGOUT_STATUS = [
        'pending' => 1,
        'approve' => 0
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'address',
        'dob',
        'gender',
        'marital_status',
        'phone',
        'status',
        'is_active',
        'avatar',
        'leave_allocated',
        'employment_type',
        'user_type',
        'joining_date',
        'workspace_type',
        'remarks',
        'uuid',
        'fcm_token',
        'device_type',
        'logout_status',
        'company_id',
        'online_status',
        'branch_id',
        'department_id',
        'post_id',
        'role_id',
        'supervisor_id',
        'office_time_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at',
        'employee_code'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->id ?? AppHelper::findAdminUserAuthId();
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();
        });
    }


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id')->select('name', 'id');
    }

    public function officeTime(): BelongsTo
    {
        return $this->belongsTo(OfficeTime::class, 'office_time_id', 'id')->select('id', 'opening_time', 'closing_time', 'shift');
    }

    public function employeeAttendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'user_id', 'id');
    }

    public function regularization(): HasMany
    {
        return $this->hasMany(Regularization::class, 'user_id', 'id');
    }

    public function employeeTodayAttendance(): HasOne
    {
        return $this->hasOne(Attendance::class, 'user_id', 'id')
            ->where('attendance_date', Carbon::now()->format('Y-m-d'));
    }

    public function employeeWeeklyAttendance(): HasMany
    {
        $currentDate = Carbon::now();
        $weekStartDate = AttendanceHelper::getStartOfWeekDate($currentDate);
        $weekEndDate = AttendanceHelper::getEndOfWeekDate($currentDate);
        return $this->hasMany(Attendance::class, 'user_id', 'id')
            ->where('attendance_status', 1)
            ->whereBetween('attendance_date', [$weekStartDate, $weekEndDate])
            ->orderBy('attendance_date', 'ASC');
    }

    public function accountDetail(): HasOne
    {
        return $this->hasOne(EmployeeAccount::class, 'user_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function scopeNotAdmin($query)
    {
        return $query->whereHas('role', function ($query) {
            $query->where('name', '!=', 'admin');
        });
    }

    public function creatorId()
    {
        if($this->role->name == 'company' || $this->role->name == 'admin')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }

    public function barcodeFormat()
    {
        $settings = Utility::settings();
        return isset($settings['barcode_format'])?$settings['barcode_format']:'code128';
    }

    public function barcodeType()
    {
        $settings = Utility::settings();
        return isset($settings['barcode_type'])?$settings['barcode_type']:'css';
    }

    public function purchaseNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["purchase_prefix"] . sprintf("%05d", $number);
    }

    public function currencySymbol()
    {
        $settings = Utility::settings();

        return $settings['site_currency_symbol'];
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();

        return date($settings['site_time_format'], strtotime($time));
    }

    public function priceFormat($price)
    {
        $settings = Utility::settings();
        $decimal_number = Utility::getValByName('decimal_number') ? Utility::getValByName('decimal_number') : 0;

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, $decimal_number) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public static function priceFormats($price)
    {
        $settings = Utility::settings();
        $decimal_number = Utility::getValByName('decimal_number') ? Utility::getValByName('decimal_number') : 0;

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, $decimal_number) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public function ownerId()
    {
        if($this->role->name == 'company' || $this->role->name == 'admin')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }    

    public function assetAssignments(){
        return $this->hasMany(AssetAssignment::class);
    }

    public function isUser()
    {
        return $this->role->name === 'user' ? 1 : 0;
    }

}
