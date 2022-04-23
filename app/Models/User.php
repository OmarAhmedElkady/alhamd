<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id' ,
        'name',
        'email',
        'photo' ,
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at' ,
        'updated_at' ,
    ];


    ////////////////////////////Start Accessors/////////////////////////////////

    // Get the entire path of the image
    public function getPhotoAttribute ($val)    {
        return $val = asset( 'assets\admin\img\user\\' . $val) ;
    }
    ////////////////////////////ُEnd Accessors/////////////////////////////////


    ////////////////////////////////////////Start Scope////////////////////////////////////////
    public function scopeSelection($q)    {
        return $q->select('id' , 'name' , 'email' , 'photo') ;
    }
    ////////////////////////////////////////End Scope////////////////////////////////////////

}
