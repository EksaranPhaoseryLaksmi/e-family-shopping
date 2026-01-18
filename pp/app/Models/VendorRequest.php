<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','user_id','store_name', 'owner_name', 'email', 'phone', 'address', 'location',
        'photos', 'delivery', 'payment', 'help', 'store_type', 'status'
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}
public function products()
{
    return $this->hasMany(Product::class, 'vendor_request_id');
}
public function orders()
{
    return $this->hasMany(Order::class, 'vendor_id');
}
}
