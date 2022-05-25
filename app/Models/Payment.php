<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


    protected $fillable = [ 'customer_id' , 'payment' , 'created_at' ];


    public function customer()  {
        return $this->belongsTo(customer::class , 'customer_id' , 'translation_of') ;
    }
}
