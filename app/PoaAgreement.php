<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\File;

class PoaAgreement extends BaseModel implements Auditable
{
	use HasRoles;
    use \OwenIt\Auditing\Auditable;

    public static $fileSystem  = 'public';
    public static $storageFolderName = 'poa';

    protected $fillable = [
        'text',
        'file',
        'is_removed',
    ];

    public function getFileAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        $storageFolderName = (str_ireplace("\\", "/", self::$storageFolderName));
        return Storage::disk(self::$fileSystem)->url($storageFolderName . '/' . $value);
    }

    public function getFileUrl()
    {
        $file = $this->getAttributes()['file'];
        $url  = NULL;

        if (!empty($file)) {
            /* $storageFolderName = (str_ireplace("\\", "/", self::$storageFolderName));
            $url               = Storage::disk(self::$fileSystem)->url($storageFolderName . '/' . $file); */
            $url = storage_path("app/public/" . self::$storageFolderName . '/' . $file);

            if (!File::exists($url)) {
                $url = NULL;
            }
        }

        return $url;
    }
}
