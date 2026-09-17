<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public $table = "invoice_data";
    protected $fillable = ['invoice_url', 'restaurant_name', 'restaurant_id','table_name','table_id','order_id','customer_code','payment_mode','status'];
}
