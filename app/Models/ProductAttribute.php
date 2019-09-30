<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    public $timestamps = false;

    protected $table = 'product_attribute';
    protected $primaryKey = 'product_id,attribute_value_id';
    
}
