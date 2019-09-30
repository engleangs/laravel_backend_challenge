<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    public $timestamps = false;

    protected $table = 'order_detail';
    protected $hidden = ['order_id'];
    protected $appends = [
        'subtotal',
      ];
      
      public function getSubtotalAttribute() {
        $subtotal = \floatval( $this->quantity  * $this->unit_cost );
        return $subtotal.'';
      }
    protected $primaryKey = 'item_id';
    protected $fillable = ['unit_cost',
                            'attributes',
                            'product_name',
                            'quantity',
                            'product_id'
                        ];
}
