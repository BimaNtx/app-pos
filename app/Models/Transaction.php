<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, Prunable;

    /**
     * Number of days before soft-deleted transactions are permanently deleted.
     */
    public const PRUNE_AFTER_DAYS = 30;

    /**
     * Get the prunable model query.
     * This will permanently delete soft-deleted transactions after 30 days.
     */
    public function prunable(): Builder
    {
        return static::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(self::PRUNE_AFTER_DAYS));
    }

    /**
     * Get the number of days remaining before this transaction is permanently deleted.
     * Returns null if the transaction is not soft-deleted.
     */
    public function getDaysUntilPermanentDeleteAttribute(): ?int
    {
        if (!$this->deleted_at) {
            return null;
        }

        $deleteDate = $this->deleted_at->addDays(self::PRUNE_AFTER_DAYS);
        $daysRemaining = (int) now()->diffInDays($deleteDate, false);

        return max(0, $daysRemaining);
    }
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
        'tax_percentage',
        'tax_amount',
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
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
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
