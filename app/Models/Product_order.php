<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_order extends Model
{
    use HasFactory;

    protected $fillable = [ 'id' , 'product_id' , 'order_id' , 'quantity'];

    public function product()   {
        return $this->hasMany(Product::class , 'translation_of' , 'product_id') ;
    }
}
