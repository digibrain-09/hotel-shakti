<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenOrder extends Model
{
    use HasFactory;
    public $table = "kitchen_orders";

    protected $fillable = ['table_name','kitchen_json', 'status','order_id'];
}
