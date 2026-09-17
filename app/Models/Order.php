<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public $table = "orders";

    protected $fillable = ['customer_code', 'order_type', 'data', 'status' ,'is_notified','total_amount','order_confirm','order_confirm_by','order_notified','cgst','sgst','taxable_amount','checkout_with_tax','coupon_code','discount','kitchen_order_status','kitchen_order_notified','manager_kitchen_order_notified','waiter_kitchen_order_notified'];
}
