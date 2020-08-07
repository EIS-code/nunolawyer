<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;
use Illuminate\Support\Facades\Storage;
use Arr;

class ClientDocument extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file',
        'is_removed',
        'client_id',
    ];

    protected $hidden = ['is_removed'];

    public static $fileSystem  = 'public';
    public static $storageFolderName = 'documents';

    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'file'      => ['string', 'nullable'],
            'client_id' => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
        ]);

        if ($returnBoolsOnly === true) {
            return !$validator->fails();
        }

        return $validator;
    }

    public function getFileAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        $storageFolderName = (str_ireplace("\\", "/", self::$storageFolderName));
        return Storage::disk(self::$fileSystem)->url('client' . '\\' . $this->client_id . '\\'. $storageFolderName . '/' . $value);
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
