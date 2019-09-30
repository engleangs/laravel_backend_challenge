<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public $timestamps = false;

    protected $table = 'attribute';
    protected $primaryKey = 'attribute_id';

    public function attribute_values(){
        return $this->hasMany(AttributeValue::class,'attribute_id');
    }
    public static function inProduct(int $product_id) {
        $attributes = ProductAttribute::where('product_id',$product_id)
                ->leftJoin('attribute_value','attribute_value.attribute_value_id','product_attribute.attribute_value_id')
                ->leftJoin('attribute','attribute_value.attribute_id','attribute.attribute_id')
                ->select(
                    'attribute.name as attribute_name',
                    'attribute_value.value as attribute_value',
                    'attribute_value.attribute_value_id'
                 )->get()
                ;
        return $attributes;
    }

}
