<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    public $timestamps = false;
    protected $table = 'tax';
    protected $primaryKey = 'tax_id';
}
