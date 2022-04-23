<?php

namespace App\Models;

use Laratrust\Models\LaratrustPermission;

class Permission extends LaratrustPermission
{
    public $guarded = [];

    protected $hidden = [ 'id' , 'display_name' , 'description' , 'pivot' ,'created_at' , 'updated_at'] ;
}
