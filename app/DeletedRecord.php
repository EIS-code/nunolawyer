<?php

namespace App;

class DeletedRecord extends BaseModel
{
    protected $fillable = [
        'model',
        'data',
        'deleted_by',
        'ip'
    ];
}
