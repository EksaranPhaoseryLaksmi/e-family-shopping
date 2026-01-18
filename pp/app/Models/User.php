<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Helper to check role
    public function isAdmin() { return $this->role === 'admin'; }
    public function isSeller() { return $this->role === 'seller'; }
    public function isUser() { return $this->role === 'user'; }

    public function vendor_requests()
    {
    return $this->hasMany(VendorRequest::class);
    }
}

