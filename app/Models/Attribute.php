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

}
