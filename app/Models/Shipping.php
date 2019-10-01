<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    public $timestamps = false;

    protected $table = 'shipping';
    protected $primaryKey = 'shipping_id';

    public static function getShippingInRegion(int $region_id) {
        
        return ShippingRegion::where('shipping_region.shipping_region_id',$region_id)
        ->leftJoin('shipping','shipping.shipping_region_id','shipping_region.shipping_region_id')
        ->select(
                    'shipping.shipping_id',
                    'shipping.shipping_type',
                    'shipping_cost',
                    'shipping.shipping_region_id'
                )
        ->get();
    }
    public static function getShippingWithCost(int $shipping_id) {
            return Shipping::where('shipping_id', $shipping_id)
                    ->leftJoin('shipping_region','shipping_region.shipping_region_id','shipping.shipping_region_id')
                    ->select('shipping_type','shipping_cost','shipping_region')
                    ->first();
    }
}
