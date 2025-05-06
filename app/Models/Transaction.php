<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'user_id', // Changed from cashier_id
        'invoice_number',
        'total_amount',
        'tax',
        'discount',
        'grand_total',
        'cash_received',
        'change',
        'payment_method',
        'status',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Changed from cashier() to user()
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function scopeForCashier($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
