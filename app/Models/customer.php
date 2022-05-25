<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    use HasFactory;

    protected $fillable = [ 'id' , 'abbr' , 'translation_of' , 'name' , 'phone' , 'title' , 'client_permissions' , 'previous_account' ];


    ////////////////////////////////////////Start Scope////////////////////////////////////////
    public function scopeSelection($q)  {
        return $q->select( 'translation_of', 'name' ,'phone' , 'title' , 'client_permissions' , 'previous_account' ) ;
    }


    public function scopeTranslationOf($q , $translation_of)  {
        return $q->where('translation_of' , $translation_of)->Abbr() ;
    }


    public function scopeAbbr($q)   {
        return $q->where('abbr' , get_default_language()) ;
    }

    ////////////////////////////////////////End Scope////////////////////////////////////////



    public function orders() {
        return $this->hasMany(Order::class , 'client_id' , 'translation_of') ;
    }

    public function payments() {
        return $this->hasMany(Payment::class , 'customer_id' , 'translation_of') ;
    }
}
