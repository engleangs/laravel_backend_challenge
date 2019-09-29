<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
/**
 * Class Review
 * @package App\Models
 * @property int $review_id
 * @property string $customer_id   
 * @property string $product_id
 * @property double $review
 * @property double $rating
 * @property string $created_on
 *
 */
class Review extends Model
{
    public $timestamps = false;

    protected $table = 'review';
    protected $primaryKey = 'review_id';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'review','rating','customer_id'
    ];
    public static function fetchListByProductId( $product_id) {
        $data = self::where('product_id', '=', $product_id )->get();
        return $data;
    }
    
    public function save(array $options = [])
    {
        $this->created_on = date("Y-m-d H:i:s");
       // before save code 
       $result = parent::save($options); // returns boolean
       // after save code
       return $result;
 
    }
    
}