<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use App\Models\ShippingRegion;
use Illuminate\Http\Request;

/**
 * The Shipping Controller contains all the methods that handles all shipping request
 * This piece of code work fine, but you can test and debug any detected issue
 *
 * Class ShippingController
 * @package App\Http\Controllers
 */
class ShippingController extends Controller
{

    /**
     * Returns a list of all shipping region.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShippingRegions()
    {   
        $shipping_regions = ShippingRegion::all();
        return response()->json( $shipping_regions );
    }
    /**
     * 
     */
    public function getShippingInRegion($shipping_region_id){
        $shppings = Shipping::getShippingInRegion( $shipping_region_id );
        return response()->json( $shppings );
    }

    /**
     * Returns a list of shipping type in a specific shipping region.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShippingType($type_id)
    {
        $shipping_region =  Shipping::where('shipping_region_id', $type_id)->first();
        if( is_null( $shipping_region) ) {
            return response()->json( [ 'error' => construct_error(404,'SHP_01','Shipping not exist','shipping_id')],404);
        }
        return response()->json( $shipping_region );
    }
}
