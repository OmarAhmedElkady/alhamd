<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [ 'id' , 'name' , 'abbr' ];

    protected $hidden = [
        'deleted_at' ,
        'created_at' ,
        'updated_at' ,
    ];

    public function scopeSelection($q)   {
        return $q->select('id' , 'abbr' , 'name') ;
    }

}
