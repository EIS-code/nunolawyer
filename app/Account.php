<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;
use App\PurposeArticle;

class Account extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'created_by',
        'client_id',
        'date',
        'received_amount',
        'purpose_article_id',
        'is_removed'
    ];

    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'created_by' 			=> ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
            'client_id' 			=> ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
            'date'      			=> ['required','date_format:Y-m-d'],
            'received_amount'   	=> ['required'],
            'purpose_article_id'	=> ['required', 'integer', 'exists:' . PurposeArticle::getTableName() . ',id']
        ]);

        if ($returnBoolsOnly === true) {
            return !$validator->fails();
        }

        return $validator;
    }

    public function getCreatedByAttribute($value)
    {
        if (!empty($value)) {
            $getClientNames = Client::getClientNames($value);

            if (!empty($getClientNames)) {
                return $getClientNames;
            }
        }

        return $value;
    }

    public function getClientIdAttribute($value)
    {
        if (!empty($value)) {
            $getClientNames = Client::getClientNames($value);

            if (!empty($getClientNames)) {
                return $getClientNames;
            }
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

    public function getReceivedAmountAttribute($value)
    {
        if (!empty($value)) {
            return number_format($value, 2);
        }

        return $value;
    }

    public function getPurposeArticleIdAttribute($value)
    {
        if (!empty($value)) {
            return PurposeArticle::getNameById($value);
        }

        return $value;
    }
}
