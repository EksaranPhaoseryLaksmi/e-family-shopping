<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\VendorRequest;
use App\Models\product;

class Order extends Model
{
    protected $fillable = [
    'vendor_id',
    'user_id',
    'delivery_name',
    'delivery_address',
    'delivery_email',
    'delivery_phone',
    'delivery_map',
    'product_name',
    'quantity',
    'total_price',
    'status',
    'product_id',
    'size',
    'receipt_no',
    'receipt_image'
];
public function product()
{
    return $this->belongsTo(Product::class);
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(VendorRequest::class);
    }
}
