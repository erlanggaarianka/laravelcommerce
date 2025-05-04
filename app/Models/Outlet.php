<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone'
    ];

    /**
     * Relationship with Users (Cashiers)
     */
    public function cashiers()
    {
        return $this->hasMany(User::class)->where('role', 'Cashier');
    }

    /**
     * Get all users (including owners for admin purposes)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
