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

    
    public static function limit_description($description_length , $items ) {
    

        foreach( $items as $item) {

            $item->description =  trim_product_description( $item->description , $description_length );
            
        }
        return $items;
    }

    public static function productsInCategory( $category_id, $current_page, $limit ,$description_length ) {
        $query_instance     =   DB::table('product')
                                ->leftJoin('product_category', 'product.product_id', '=', 'product_category.product_id')
                                ->where('product_category.category_id','=', $category_id)
                                ->select('product.*')
                                ;
        $paginate_data      = $query_instance->paginate( $limit, ["*"], 'page', $current_page );
        $items              =  self::limit_description( $description_length ,  $paginate_data->items() );
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
        $items                  = self::limit_description( $description_length , $items);
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
        return self::all();
    }

    public static function searchProduct($query_string , $all_words, $current_page, $limit ,$description_length) {

        $query_instance     =  self::where('name','like','%'.$query_string.'%');
        if( $all_words == "on") {
            $query_instance->orWhere('description', 'like', '%'.$query_string.'%');
        }
        $paginate_data      = $query_instance->paginate( $limit, ["*"], $current_page);
        $items              = $paginate_data->items();
        $items              = self::limit_description( $description_length , $items);
        return $items;                    
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
