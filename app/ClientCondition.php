<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;

class ClientCondition extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'condition',
        'client_id',
    ];
    
    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'date'      => ['required','date_format:Y-m-d'],
            'condition' => ['string', 'nullable'],
            'client_id' => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
        ]);

        if ($returnBoolsOnly === true) {
            return !$validator->fails();
        }

        return $validator;
    }

    public function getDateAttribute($value)
    {
        return (!empty($value)) ? date('Y-m-d', strtotime($value)) : $value;
    }

    /*public function setDateAttribute($value)
    {
        return (!empty($value)) ? date('Y-m-d', strtotime($value)) : $value;
    }*/
}
