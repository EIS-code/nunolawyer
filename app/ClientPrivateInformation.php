<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;

class ClientPrivateInformation extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'client_private_informations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'private_information',
        'is_removed',
        'client_id',
    ];

    protected $hidden = ['is_removed'];

    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'date'                => ['required','date_format:Y-m-d'],
            'private_information' => ['string', 'max:255', 'nullable'],
            'client_id'           => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
        ]);

        if ($returnBoolsOnly === true) {
            return !$validator->fails();
        }

        return $validator;
    }
}
