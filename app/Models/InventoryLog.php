<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_id',
        'user_id',
        'quantity',
        'reason',
        'remaining_stock'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'remaining_stock' => 'integer'
    ];

    /**
     * Get the inventory record associated with the log.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the user who made the adjustment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for additions (positive quantities)
     */
    public function scopeAdditions($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope for removals (negative quantities)
     */
    public function scopeRemovals($query)
    {
        return $query->where('quantity', '<', 0);
    }

    /**
     * Get the adjustment type (add/remove)
     */
    public function getTypeAttribute()
    {
        return $this->quantity > 0 ? 'Addition' : 'Removal';
    }

    /**
     * Get absolute quantity (without sign)
     */
    public function getAbsoluteQuantityAttribute()
    {
        return abs($this->quantity);
    }
}
