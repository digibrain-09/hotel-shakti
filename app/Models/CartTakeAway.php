<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartTakeAway extends Model
{
    use HasFactory;

    public $table = "carts_takeaway";

    protected $fillable = ['item_id', 'name','price','quantity','addons','note','image'];
}
