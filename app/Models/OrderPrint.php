<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPrint extends Model
{
    use HasFactory;
    public $table = "order_print";

    protected $fillable = ['customer_code', 'item_data', 'status' ,'order_id','table_id'];
}
