<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;

class PurposeArticle extends BaseModel implements Auditable
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
        'text',
        'is_removed',
    ];

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
}
