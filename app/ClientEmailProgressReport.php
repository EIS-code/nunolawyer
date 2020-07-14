<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;
use App\Client;
use Illuminate\Support\Facades\Storage;

class ClientEmailProgressReport extends BaseModel implements Auditable
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
        'progress_report',
        'file',
        'is_removed',
        'client_id',
    ];

    protected $hidden = ['is_removed'];

    public static $fileSystem  = 'public';
    public static $storageFolderName = 'report_files';

    public static function validators(array $data, $returnBoolsOnly = false)
    {
        $validator = Validator::make($data, [
            'date'            => ['required','date_format:Y-m-d'],
            'progress_report' => ['string', 'max:255'],
            'file'            => ['string', 'nullable'],
            'client_id'       => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
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

        $storageFolderName = (str_ireplace("\\", "/", $this->storageFolderName));
        return Storage::disk($this->fileSystem)->url('client' . '\\' . $this->client_id . '\\'. $storageFolderName . $value);
    }
}
