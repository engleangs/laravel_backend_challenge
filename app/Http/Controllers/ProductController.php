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
        $description_length = $request->input('description_length', 200);
        if( !is_numeric($description_length)) {
            return response()->json(
                [   
                    'error'=>construct_error( 400,'PRD_03','Description length is invalid','description_length')
                ],
                404
            );
        }
        $description_length = intval(  $description_length );
        $product            = Product::where('product_id', $id )->first();
        if (is_null($product)) {
            return response()->json(
                                    [   
                                        'error'=>construct_error( 404,'PRD_01','Product does not exist','product_id')
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
    public function getProductsInDepartment(Request $request ,$department_id)
    {
        $department_id = intval( $department_id );
        [ $page ,$limit ,$description_length  ] = query_product_param( $request);
        $items = Product::countedAndPaginableResultsWithDepartments([
                'current_page'=>$page,
                'limit'=>$limit,
                'description_length'=> $description_length,
                'department_id'=>$department_id
        ]);
        return response()->json( $items );
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
            return response()->json( [ 'error' => construct_error(404,'DEP_02','Department not exist','department_id')],404);
        }   
        return response()->json($department );

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
            return response()->json( [ 'error' => construct_error(404,'DEP_02','Department not exist','department_id')],404);
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
        $category   = Product::getCategoryOfProduct( $product_id );
        if( is_null( $category ) )  {
            return response()->json( [ 'error' => construct_error(404,'PRD_02','Product not exist','product_id')],404);
        }
        return response()->json($category);
    }
    /**
     * Returns a category of  a particular id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleCategory($category_id ){
        $category_id = intval( $category_id );
        $category    = Category::find( $category_id );
        if( is_null( $category ) ) {
            return response()->json( [ 'error' => construct_error(404,'CAT_01','Category not exist','department_id')],404);
        }
        return response()->json($category);
    }

     /**
     * Returns list of reviews of  a particular product
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getProductReview($product_id) {
        $product_id = intval( $product_id );
        $reviews    = Review::fetchListByProductId( $product_id  );
        return response()->json([ $reviews ]);
    }

    /**
     * Returns review  of  a particular product
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function postProductReview(Request $request, $product_id) {
        //since product_id alrady present in url this one is duplicate
        $user  = $request->user();
        $customer_id = $user->getKey();
        $data = $this->getRequestData($request, [ 'product_id', 'review','rating']);
        $validator = Validator::make($data, [
            'review' => 'required',
            'rating' => 'required|numeric',
            'product_id'=>'numeric|required'
            ],
            error_message_for_review()
        );
        if($validator->fails()) {
            $errors = format_input_error( $validator->errors()->first() );
            return  response()->json(['error'=>$errors],400);          
        }
        $data[ 'customer_id' ] = $customer_id;
        $review = Review::create( $data );
        $review_with_product  = Review::reviewWithProduct( $review->review_id);
        return  response()->json( 
                    $review_with_product
            , 201);       
    }



}
