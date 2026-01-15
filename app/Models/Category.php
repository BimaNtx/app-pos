<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'bg_color',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the product count for this category.
     */
    public function getProductCountAttribute(): int
    {
        return $this->products()->count();
    }

    /**
     * Available color options for categories.
     */
    public static function colorOptions(): array
    {
        return [
            ['color' => 'text-orange-700', 'bg' => 'bg-orange-100', 'label' => 'Orange'],
            ['color' => 'text-blue-700', 'bg' => 'bg-blue-100', 'label' => 'Blue'],
            ['color' => 'text-pink-700', 'bg' => 'bg-pink-100', 'label' => 'Pink'],
            ['color' => 'text-green-700', 'bg' => 'bg-green-100', 'label' => 'Green'],
            ['color' => 'text-purple-700', 'bg' => 'bg-purple-100', 'label' => 'Purple'],
            ['color' => 'text-red-700', 'bg' => 'bg-red-100', 'label' => 'Red'],
            ['color' => 'text-yellow-700', 'bg' => 'bg-yellow-100', 'label' => 'Yellow'],
            ['color' => 'text-teal-700', 'bg' => 'bg-teal-100', 'label' => 'Teal'],
            ['color' => 'text-gray-700', 'bg' => 'bg-gray-100', 'label' => 'Gray'],
        ];
    }

    /**
     * Available icon options for categories.
     */
    public static function iconOptions(): array
    {
        return ['🍚', '🥤', '🍰', '🍜', '🍕', '🍔', '🌮', '🍣', '🍦', '☕', '🧃', '🍺', '🥗', '🍝', '🥘', '🏷️'];
    }
}
