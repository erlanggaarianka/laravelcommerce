<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'outlet_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship with Outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Scope for Cashiers
     */
    public function scopeCashiers($query)
    {
        return $query->where('role', 'Cashier');
    }

    /**
     * Scope for Owners
     */
    public function scopeOwners($query)
    {
        return $query->where('role', 'Owner');
    }

    /**
     * Check if user is a cashier
     */
    public function isCashier()
    {
        return $this->role === 'Cashier';
    }

    /**
     * Check if user is an owner
     */
    public function isOwner()
    {
        return $this->role === 'Owner';
    }
}
