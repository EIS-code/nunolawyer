<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;

class ClientTermsAndCondition extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'terms_and_conditions',
        'is_removed',
        'client_id',
    ];

    protected $hidden = ['is_removed'];

    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'terms_and_conditions' => ['required','string', 'max:255'],
            'client_id'            => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
        ]);

        if ($returnBoolsOnly === true) {
            return !$validator->fails();
        }

        return $validator;
    }
}
