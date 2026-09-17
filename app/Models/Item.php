<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    public $table = "items";

    protected $fillable = ['item_name', 'description', 'price','picture', 'category_id', 'restaurant_id'];

    public function items()
    {
        return $this->belongsTo(Category::class);
    }
}
