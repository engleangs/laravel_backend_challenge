<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Category;
use App\Models\Review;
use App\Models\Customer;
use Validator;

/**
 * The Product controller contains all methods that handles product request
 * Some methods work fine, some needs to be implemented from scratch while others may contain one or two bugs/
 *
 *  NB: Check the BACKEND CHALLENGE TEMPLATE DOCUMENTATION in the readme of this repository to see our recommended
 *  endpoints, request body/param, and response object for each of these method.
 */
class ProductController extends Controller
{

    /**
     * Return a paginated list of products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllProducts(Request $request)
    {
        [ $page ,$limit ,$description_length ] = query_product_param( $request );  
        $data = Product::countedAndPaginableResults(
                    [
                        "current_page"=> $page,
                        "limit"=> $limit,
                        "description_length"=>$description_length
                    ]
            );
        return response()->json( $data );
    }

    /**
     * Returns a single product with a matched id in the request params.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProduct( Request $request, $product_id )
    {
        $id                 = intval( $product_id );
        $description_length = intval( $request->input('description_length', 200) );
        $product            = Product::where('product_id', $id )->first();
        if (is_null($product)) {
            return response()->json(
                                    [   
                                        'status' => false,
                                        'message'=>'Not found'
                                    ],
                                    404
                                );
        }
        $product->description = trim_product_description( $product->description , $description_length);
        return response()->json( $product );
    }

    /**
     * Returns a list of product that matches the search query string.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProduct(Request $request)
    {
        [ $page ,$limit ,$description_length  ] = query_product_param( $request );  
        $query      = $request->input('query_string','');
        $all_words  = $request->input('all_words', 'off');
        $data       = Product::searchProduct( $query, $all_words , $page, $limit , $description_length );
        return response()->json( ['rows' =>$data ]);
    }

    /**
     * Returns all products in a product category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsByCategory( Request $request, $category_id )
    {
        [ $page ,$limit ,$description_length  ] = query_product_param( $request );  
        $items     = Product::productsInCategory( $category_id , $page, $limit, $description_length);
        return response()->json([ 'rows' => $items ]);
    }

    /**
     * Returns a list of products in a particular department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsInDepartment()
    {
        return response()->json(['message' => 'this works']);
    }

    /**
     * Returns a list of all product departments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDepartments()
    {
        $departments = Department::all();
        return response()->json( $departments );
    }

    /**
     * Returns a single department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepartment($department_id)
    {
        $id = intval( $department_id );
        $department = Department::find( $id);
        if( is_null($department) ) {
            return response()->json([ 'message' =>'Could not find department'],404);
        }   
        return response()->json(['status' => false, 'department' => $department]);

    }

    /**
     * Returns all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCategories()
    {
        $categories = Category::all();
        return response()->json( $categories );
    }

    /**
     * Returns all categories in a department.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepartmentCategories($department_id)
    {
        $id         = intval( $department_id );
        $deparment  = Department::where('department_id', $id)->first();
        if( is_null( $deparment) ) {
            return response()->json([ 'message' =>'Could not find department'],404);
        }
        $category = $deparment->category;
        return response()->json([ 'rows'=> $category ]);
    }

    /**
     * Returns a category of  a particular product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductCategory($product_id)
    {
        $id         = intval($product_id);
        $product    = Product::where( 'product_id', $id )->first();
        if( is_null( $product ) )  {
            return response()->json(['message' => 'Not found'], 404);
        }
        $category  = $product->categories->first();
        return response()->json($category);
    }

     /**
     * Returns list of reviews of  a particular product
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getProductReview($product_id) {
        
        $customer = Customer::where('customer_id',1)->get()->first();
        $product_id = intval( $product_id );
        $reviews    = Review::fetchListByProductId( $product_id  );
        return response()->json($reviews);
    }

    /**
     * Returns review  of  a particular product
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function postProductReview(Request $request, $product_id) {
        if ($request->isJson()) {
            $data = $request->json()->all();
        } else {
            $data = $request->all();
        }
        $validator = Validator::make($data, [
            'customer_id' => 'required|numeric',
            'review' => 'required',
            'rating' => 'required|numeric'
        ]);
        if($validator->fails()) {
            return  response()->json(['message'=> $validator->errors() ],400);       
        }
        $data[ 'product_id' ] = intval($product_id); 
        $review = Review::create( $data );
        return  response()->json( $review , 201);       
    }



}
