<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    // Product.php
    protected $fillable = [
        'vendor_request_id', 'name', 'description', 'image', 'variants'
    ];


    protected $casts = [
        'variants' => 'array',
        'image' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(\App\Models\VendorRequest::class, 'vendor_request_id');
    }

}
