<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'expires_at'];

    public function isValid()
    {
        return is_null($this->expires_at) || Carbon::now()->lte($this->expires_at);
    }
}
