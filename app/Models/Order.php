<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [ 'id' , 'client_id' , 'total_price' , 'status' ];



    ////////////////////////////////////////Start Scope////////////////////////////////////////
    public function scopeSelection($q)  {
        return $q->select('id' , 'client_id' , 'total_price' , 'status' , 'created_at')->orderBy('created_at' , 'DESC' ) ;
    }
    ////////////////////////////////////////End Scope////////////////////////////////////////


    public function getDateForHumansAttribute()   {
        return $this->created_at->diffForhumans();
    }

    public function toArray()  {
        $data = parent::toArray();

        $data['DiffForHumans'] = $this->DateForHumans;

        return $data;
    }

    ////////////////////////////////////////Start Relations////////////////////////////////////////
    public function customer()  {
        return $this->belongsTo(customer::class , 'client_id' , 'translation_of') ;
    }

    public function product_order()   {
        return $this->hasMany(Product_order::class , 'order_id' , 'id') ;
    }
    ////////////////////////////////////////End Relations////////////////////////////////////////
}
