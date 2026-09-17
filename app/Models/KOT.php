<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KOT extends Model
{
    use HasFactory;
    public $table = "order_kot";

    protected $fillable = ['customer_code', 'url', 'table_id', 'order_id', 'restaurant_id'];
}
