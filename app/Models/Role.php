<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $guarded = [];


    protected $hidden = [ 'display_name' , 'description' ,  'pivot' ,'created_at' , 'updated_at'] ;
}
