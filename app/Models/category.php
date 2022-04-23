<?php

namespace App\Models;

// use App\Observers\Admin\CategoryObserve;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class category extends Model
{
    use HasFactory;
    use SoftDeletes ;
    protected $fillable = [ 'id' , 'name' , 'abbr' , 'translation_of' ];

    protected $dates = ['deleted_at'] ;

    protected $hidden = [
        'deleted_at' ,
        'created_at' ,
        'updated_at' ,
    ];


    ////////////////////////////////////////Start Scope////////////////////////////////////////
    public function scopeSelection($q)  {
        return $q->select( 'id', 'translation_of' ,'name' ) ;
    }

    public function scopeLanguageSelect($q)   {
        return $q->where('abbr' , get_default_language()) ;
    }
    ////////////////////////////////////////End Scope////////////////////////////////////////


    // associate model with observe
    // protected static function boot()    {
    //     parent::boot() ;
    //     category::observe(CategoryObserve::class) ;
    // }


    ////////////////////////////////////////Start Relations////////////////////////////////////////
    public function products()  {
        return $this->hasMany(Product::class , 'category_id' , 'translation_of' ) ;
    }
    ////////////////////////////////////////End Relations////////////////////////////////////////

}
