<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Validator;

class Email extends BaseModel implements Auditable
{
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'from',
        'to',
        'cc',
        'bcc',
        'subject',
        'body',
        'is_send',
        'exception_info',
        'created_at'
    ];

    public static function validator(array $data)
    {
        return Validator::make($data, [
            'to.*'    => ['required', 'email'],
            'subject' => ['required', 'string'],
            'body'    => ['required'],
        ]);
    }
}
