<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;

class FollowUp extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'date',
        'follow_by',
        'follow_from',
        'client_id',
        'is_removed'
    ];

    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'date'        => ['date_format:Y-m-d'],
            'follow_by'   => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
            'follow_from' => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
            'client_id'   => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
        ]);

        if ($returnBoolsOnly === true) {
            if ($validator->fails()) {
                // \Session::flash('error', $validator->errors()->first());
            }

            return !$validator->fails();
        }

        return $validator;
    }

    public function client()
    {
        return $this->hasOne('App\Client', 'id', 'client_id')->where('is_removed', self::$notRemoved);
    }

    public function clientFollowedBy()
    {
        return $this->hasOne('App\Client', 'id', 'follow_by')->where('is_removed', self::$notRemoved);
    }

    public function getCreatedByAttribute($value)
    {
        if (!empty($value)) {
            return date('Y-m-d', strtotime($value));
        }

        return $value;
    }

    public function getDateAttribute($value)
    {
        if (!empty($value)) {
            return date('Y-m-d', strtotime($value));
        }

        return $value;
    }

    public function clientPurposeArticles()
    {
        return $this->hasMany('App\ClientPurposeArticle', 'client_id', 'id')->where('is_removed', self::$notRemoved);
    }
}
