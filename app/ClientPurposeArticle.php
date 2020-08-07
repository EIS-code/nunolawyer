<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;
use App\PurposeArticle;
use Arr;

class ClientPurposeArticle extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purpose_article_id',
        'client_id',
        'is_removed',
        'is_last_inserted',
    ];
    
    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'purpose_article_id' => ['required', 'integer', 'exists:' . PurposeArticle::getTableName() . ',id'],
            'client_id'          => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
        ]);

        if ($returnBoolsOnly === true) {
            return !$validator->fails();
        }

        return $validator;
    }

    public function purposeArticle()
    {
        return $this->hasOne('App\PurposeArticle', 'id', 'purpose_article_id');
    }

    public function purposeArticles()
    {
        return $this->hasMany('App\PurposeArticle', 'id', 'purpose_article_id');
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
