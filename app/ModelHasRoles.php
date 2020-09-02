<?php

namespace App;

class ModelHasRoles extends BaseModel
{
    protected $table = 'model_has_roles';

    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
    ];

    public $timestamps = false;
}
