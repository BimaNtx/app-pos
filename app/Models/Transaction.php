<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'transaction_code',
        'order_type',
        'table_number',
        'payment_method',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_amount',
        'amount_received',
        'change_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    /**
     * Get the transaction details for the transaction.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Generate a unique transaction code.
     */
    public static function generateTransactionCode(): string
    {
        $prefix = 'TRX';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -5));
        
        return "{$prefix}-{$date}-{$random}";
    }
}
