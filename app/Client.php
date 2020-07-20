<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use App\PurposeArticle;

class Client extends Authenticatable implements MustVerifyEmail, Auditable
{
    use Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'registration_date',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'secondary_email',
        'dob',
        'contact',
        'passport_number',
        'process_address',
        'nationality',
        'work_status',
        'photo',
        'banned',
        'assign_date',
        'assign_to',
        'password',
        'is_superadmin',
        'last_login_at',
        'last_logout_at',
        'is_removed',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $notRemoved = '0';

    public static $removed = '1';

    const DEFAULT       = '0';
    const TO_FOLLOW     = '1';
    const WORK_DONE_ALL = '2';

    public static $workStatus = [
        self::DEFAULT       => 'Default',
        self::TO_FOLLOW     => 'To Follow',
        self::WORK_DONE_ALL => 'Work done all'
    ];

    public static $isEditors = false;

    /*public function __construct()
    {
        self::$workStatus = [
            self::DEFAULT       => __('Default'),
            self::TO_FOLLOW     => __('To Follow'),
            self::WORK_DONE_ALL => __('Work done all')
        ];

        parent::__construct();
    }*/

    public function isSuperAdmin()
    {
        return $this->is_superadmin == '1' ? true : false;
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function getClientNames(int $clientId)
    {
        if (!empty($clientId)) {
            $getClient = self::where('id', '=', (int)$clientId)->where('is_removed', self::$notRemoved)->first();

            if (!empty($getClient)) {
                return $getClient->first_name . ' ' . $getClient->last_name;
            }
        }

        return NULL;
    }

    public function getAssignToAttribute($value)
    {
        if (!empty($value)) {
            return $this->getClientNames($value);
        }

        return $value;
    }

    public function getWorkStatusAttribute($value)
    {
        if (array_key_exists($value, self::$workStatus)) {
            return self::$workStatus[$value];
        }

        return $value;
    }

    public function clientPurposeArticles()
    {
        return $this->hasMany('App\ClientPurposeArticle', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }

    public static function getAllClientPurposeArticles()
    {
        return PurposeArticle::where('is_removed', self::$notRemoved)->get();
    }

    public function clientConditions()
    {
        return $this->hasMany('App\ClientCondition', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }

    public function clientFees()
    {
        return $this->hasMany('App\ClientFee', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }

    public function clientEmailProgressReports()
    {
        return $this->hasMany('App\ClientEmailProgressReport', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }

    public function clientPrivateInformations()
    {
        return $this->hasMany('App\ClientPrivateInformation', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }

    public function clientDocuments()
    {
        return $this->hasMany('App\ClientDocument', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }

    public function clientTermsAndConditions()
    {
        return $this->hasMany('App\ClientTermsAndCondition', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }

    public function translateModelDocuments()
    {
        return $this->hasMany('App\TranslateModelDocument', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }
}
