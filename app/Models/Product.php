<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\ProductCategory;
use Illuminate\Support\Facades\DB;

/**
 * Class Product
 * @package App\Models
 * @property int $product_id
 * @property string $name
 * @property string $description
 * @property double $price
 * @property double $discounted_price
 * @property string $image
 * @property string $image_2
 * @property string $thumbnail
 * @property int $display
 * @property Collection $categories
 *
 */
class Product extends Model
{
    public $timestamps = false;

    protected $table = 'product';
    protected $primaryKey = 'product_id';

    
    public static function limitDescription($description_length , $items ) {
    

        foreach( $items as $item) {

            $item->description =  trim_product_description( $item->description , $description_length );
            
        }
        return $items;
    }

    public static function productsInCategory( $category_id, $current_page, $limit ,$description_length ) {
        $query_instance     =   DB::table('product')
                                ->leftJoin('product_category', 'product.product_id', '=', 'product_category.product_id')
                                ->where('product_category.category_id','=', $category_id)
                                ->select(
                                            'product.product_id',
                                            'product.name',
                                            'product.description',
                                            'product.price',
                                            'product.discounted_price',
                                            'product.thumbnail'
                                        )
                                ;
        $paginate_data      = $query_instance->paginate( $limit, ["*"], 'page', $current_page );
        $items              =  self::limitDescription( $description_length ,  $paginate_data->items() );
        return  $items;
                            
    }

    public static function countedAndPaginableResults(array $criteria = [])
    {
        $limit              = 20;
        $current_page       = 1;
        $description_length = 200;
        if( count( $criteria) > 0) {
            $limit              = $criteria[ "limit"];
            $current_page       = $criteria[ "current_page" ];
            $description_length = isset($criteria[ "description_length" ])? ($criteria[ "description_length" ]):200 ;
        }
        $paginate_data          = self::paginate( $limit,["*"], 'page',$current_page );
        $items                  = $paginate_data->items();
        $items                  = self::limitDescription( $description_length , $items);
        $data                   = [
                                    "paginationMeta"=>[
                                        "currentPage"=> $paginate_data->currentPage(),
                                        "currentPageSize"=> $paginate_data->count(),
                                        "totalRecords"=> $paginate_data->total(),
                                        "totalPages"=> $paginate_data->lastPage(),
                                        ],
                                    "rows"=> $items ,
                    ];
        return $data;
    }



    public static function countedAndPaginableResultsWithDepartments(array $criteria = [])
    {
        $department_id = $criteria['department_id'];
        $current_page  = $criteria['current_page'];
        $limit         = $criteria['limit'];
        $description_length = isset($criteria[ "description_length" ])? ($criteria[ "description_length" ]):200 ;
        $paginate_data = Category::where('department_id',$department_id)
                        ->leftJoin('product_category','product_category.category_id','category.category_id')
                        ->leftJoin('product','product.product_id','product_category.product_id')
                        ->select(
                                'product.product_id',
                                'product.name',
                                'product.description',
                                'product.price',
                                'product.discounted_price',
                                'product.thumbnail'
                                )
                        ->paginate( $limit,["*"], 'page',$current_page);
        $items = $paginate_data->items();
        $items =   self::limitDescription( $description_length , $items);
        return $items;
    }

    public static function searchProduct($query_string , $all_words, $current_page, $limit ,$description_length) {

        $query_instance     =  self::where('name','like','%'.$query_string.'%');
        if( $all_words == "on") {
            $query_instance->orWhere('description', 'like', '%'.$query_string.'%');
        }
        $paginate_data      = $query_instance->paginate( $limit, ["*"], $current_page);
        $items              = $paginate_data->items();
        $items              = self::limitDescription( $description_length , $items);
        return $items;                    
    }

    public static function getCategoryOfProduct($product_id) {
        return ProductCategory::where('product_id',$product_id)
            ->join('category','category.category_id','product_category.category_id')
            ->select('category.category_id','category.department_id','category.name')
            ->get()
            ->first();
    }
    public function categories()
    {
        return $this->hasManyThrough(
            Category::class,
            ProductCategory::class,
            'product_id',
            'category_id',
            'product_id',
            'category_id'
        );
    }
    public static function getProductInIds($product_ids) {
        $product_list = DB::table('product')->whereIn('product_id', $product_ids)
                        ->get();
        $products = [];
        foreach($product_list as $product) {
            $products[ $product->product_id]  = $product;
        }
        return $products;
    }
}
