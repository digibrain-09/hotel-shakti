<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    public $table = "restaurant_tables";

    protected $fillable = ['table_name', 'qr_code', 'restaurant_id'];
}
