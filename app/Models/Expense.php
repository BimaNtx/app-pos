<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    public const CATEGORY_BAHAN_BAKU = 'bahan_baku';
    public const CATEGORY_OPERASIONAL = 'operasional';
    public const CATEGORY_GAJI = 'gaji';
    public const CATEGORY_LAINNYA = 'lainnya';

    public const CATEGORIES = [
        self::CATEGORY_BAHAN_BAKU => 'Bahan Baku',
        self::CATEGORY_OPERASIONAL => 'Operasional',
        self::CATEGORY_GAJI => 'Gaji',
        self::CATEGORY_LAINNYA => 'Lainnya',
    ];

    protected $fillable = [
        'date',
        'category',
        'description',
        'amount',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
