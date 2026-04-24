<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSession extends Model
{
    protected $fillable = [
        'payment_ref',
        'cart',
        'amount',
        'status',
        'expires_at',
        'delivery_name',
        'delivery_address',
        'delivery_email',
        'bakong_md5',
        'delivery_phone',
        'delivery_map',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
