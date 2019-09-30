<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attribute;

/**
 * The controller defined below is the attribute controller.
 * Some methods needs to be implemented from scratch while others may contain one or two bugs.
 *
 * NB: Check the BACKEND CHALLENGE TEMPLATE DOCUMENTATION in the readme of this repository to see our recommended
 *  endpoints, request body/param, and response object for each of these method
 *
 *
 * Class AttributeController
 * @package App\Http\Controllers
 */
class AttributeController extends Controller
{
    /**
     * This method should return an array of all attributes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllAttributes()
    {
        $attribtues = Attribute::all();
        return response()->json(  $attribtues );
    }

    /**
     * This method should return a single attribute using the attribute_id in the request parameter.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleAttribute( $attribute_id )
    {
        $id     = intval( $attribute_id );
        $attribute = Attribute::where('attribute_id', $id)->first();
        if( is_null( $attribute ) ) {
            return response()->json( ['error'=>construct_error( 404,'ATR_01','Attribute does not exist','attribute_id')] ,404);    
        }
        return response()->json( $attribute );
    }

    /**
     * This method should return an array of all attribute values of a single attribute using the attribute id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttributeValues( $attribute_id )
    {
        $id             = intval( $attribute_id );
        $attribute      = Attribute::where('attribute_id', $id)->first();
        if( is_null( $attribute ) ) {
            return response()->json( ['error'=>construct_error( 404,'ATR_01','Attribute does not exist','attribute_id')] ,404);    
        }
        $attribute_values = $attribute->attribute_values;
        return response()->json( $attribute_values );
    }

    /**
     * This method should return an array of all the product attributes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductAttributes($product_id)
    {
        $product_id   = intval( $product_id );
        $attributes     = Attribute::inProduct( $product_id );
        return response()->json( $attributes );

    }
}
