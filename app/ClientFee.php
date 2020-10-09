<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;
use Arr;

class ClientFee extends BaseModel implements Auditable
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
        'proposed_lawyer_fee',
        'received_lawyer_fee',
        'missing_lawyer_fee',
        'proposed_government_fee',
        'received_government_fee',
        'missing_government_fee',
        'is_removed',
        'client_id',
    ];

    protected $hidden = ['is_removed'];

    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'date'                      => ['required','date_format:Y-m-d'],
            'proposed_lawyer_fee'       => ['string', 'nullable'],
            'received_lawyer_fee'       => ['integer', 'nullable'],
            'missing_lawyer_fee'        => ['integer', 'nullable'],
            'proposed_government_fee'   => ['string', 'nullable'],
            'received_government_fee'   => ['integer', 'nullable'],
            'missing_government_fee'    => ['integer', 'nullable'],
            'client_id'                 => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
        ]);

        if ($returnBoolsOnly === true) {
            return !$validator->fails();
        }

        return $validator;
    }

    public function transformAudit(array $data) : array
    {
        $routeName  = \Request::route()->getName();

        if (!empty($routeName) && in_array($routeName, ['clients.update', 'clients.create', 'clients.destroy', 'editors.create', 'editors.update', 'editors.destroy'])) {
            $parameters = \Request::route()->parameters();
            $clientId   = !empty($parameters['client']) ? $parameters['client'] : (!empty($parameters['id']) ? $parameters['id'] : NULL);

            Arr::set($data, 'client_id',  $clientId);
        }

        return $data;
    }
}
