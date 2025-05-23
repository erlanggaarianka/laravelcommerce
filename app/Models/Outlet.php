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
        'phone',
        'is_tax_enabled', // <-- Add this
        'tax_rate'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_tax_enabled' => 'boolean', // <-- Add this
        'tax_rate' => 'decimal:2', // Optional: Good to cast decimals too
    ];

    // ... rest of your model methods
    public function cashiers()
    {
        return $this->hasMany(User::class)->where('role', 'Cashier');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
