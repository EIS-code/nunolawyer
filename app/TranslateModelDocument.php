<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TranslateModelDocument extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'file',
        'client_id',
        'is_removed',
    ];

    public static $fileSystem  = 'public';
    public static $storageFolderName = 'translate_model_document';

    public static function getNameById(int $id)
    {
        if (!empty($id)) {
            $getData = self::where('is_removed', self::$notRemoved)->where('id', $id)->first();

            if (!empty($getData)) {
                return $getData->title;
            }
        }

        return NULL;
    }

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
            $url = storage_path("app/public/" . self::$storageFolderName . '/' . $file);

            if (!File::exists($url)) {
                $url = NULL;
            }
        }

        return $url;
    }
}
