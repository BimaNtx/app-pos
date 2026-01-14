<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logistic extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'stock',
        'minimum_stock',
    ];
}
