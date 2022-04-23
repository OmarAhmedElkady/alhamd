<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes ;


    protected $fillable = [
        'id' ,
        'abbr',
        'translation_of',
        'category_id' ,
        'name',
        'image',
        'purchasing_price',
        'pharmacist_price',
        'selling_price',
        'store',
    ];

    protected $dates = ['deleted_at'] ;


    protected $appends = [
        'pharmacistProfitRatio' ,
        'profitRateFromTheAudience' ,
        'totalProfitFromThePharmacist' ,
        'totalProfitFromTheAudience' ,
        'ProductPriceAccordingToCustomerType' ,
    ] ;



    ////////////////////////////////////////Start Get Attribute////////////////////////////////////////

    // Get the name of the image and add its path
    public function getImageAttribute ($val) {
        return $val = asset( 'assets\admin\img\products\\' . $val) ;
    }   //  End Of Image


    // Percentage of profit from the pharmacy
    public function getPharmacistProfitRatioAttribute ($val) {

        $pharmacistProfitRatio =  $this->pharmacist_price - $this->purchasing_price ;

        if ($pharmacistProfitRatio > 0 && $this->purchasing_price > 0 ) {
            $pharmacistProfitRatio = ( $pharmacistProfitRatio * 100 ) / $this->purchasing_price ;
            return number_format($pharmacistProfitRatio , 1)  ;
        }   else    {
            return 0 ;
        }   // End Of Else
    }   // End Of Percentage of profit from the pharmacy



    // Percentage of profit from the Audience
    public function getProfitRateFromTheAudienceAttribute ($val) {

        $profitRateFromTheAudience =  $this->selling_price - $this->purchasing_price ;

        if ($profitRateFromTheAudience > 0 && $this->purchasing_price > 0 ) {
            $profitRateFromTheAudience = ( $profitRateFromTheAudience * 100 ) / $this->purchasing_price ;
            return number_format($profitRateFromTheAudience , 1) ;
        }   else    {
            return 0 ;
        }   // End Of Else
    }  //  End Of Percentage of profit from the Audience



    // Total profit from the pharmacist
    public function getTotalProfitFromThePharmacistAttribute ($val) {

        $totalProfitFromThePharmacist =  $this->pharmacist_price - $this->purchasing_price ;

        if ($totalProfitFromThePharmacist > 0 && $this->store > 0) {
            $totalProfitFromThePharmacist = $totalProfitFromThePharmacist * $this->store ;
            return number_format($totalProfitFromThePharmacist , 0) ;
        }   else    {
            return 0 ;
        }   //  End If Else
    }   //  End Of Total profit from the pharmacist




    // Total profit from the audience
    public function getTotalProfitFromTheAudienceAttribute ($val) {
        $totalProfitFromTheAudience =  $this->selling_price - $this->purchasing_price ;

        if ($totalProfitFromTheAudience > 0 && $this->store > 0)   {
            $totalProfitFromTheAudience = $totalProfitFromTheAudience * $this->store ;
            return number_format($totalProfitFromTheAudience , 0) ;
        }   else    {
            return 0 ;
        }   //  End If Else
    }   //  End Of Total profit from the audience




    // Product price according to customer type
    public function getProductPriceAccordingToCustomerTypeAttribute()  {
        return (($this->selling_price - $this->pharmacist_price) / 2) + $this->pharmacist_price ;

    }

    ////////////////////////////////////////Start Get Attribute////////////////////////////////////////





    ////////////////////////////////////////Start Scope////////////////////////////////////////
    public function scopeSelection($q)  {
        return $q->select( 'id' , 'translation_of' , 'category_id', 'name' , 'image' , 'purchasing_price' , 'pharmacist_price' , 'selling_price' , 'store') ;
    }


    public function scopeAbbr($q)   {
        return $q->where('abbr' , get_default_language()) ;
    }


    ////////////////////////////////////////End Scope////////////////////////////////////////


    ////////////////////////////////////////Start Relations////////////////////////////////////////
    public function category()  {
        return $this->belongsTo(category::class , 'category_id' , 'translation_of') ;
    }
    ////////////////////////////////////////End Relations////////////////////////////////////////

}
