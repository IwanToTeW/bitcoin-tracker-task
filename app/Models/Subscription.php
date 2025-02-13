<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = ['pair', 'email', 'price', 'period', 'percentage', 'has_expired', 'valid_from'];

    protected $casts = [
        'period' => 'integer',
        'price' => 'float',
        'has_expired' => 'boolean',
        'valid_from' => 'datetime'
    ];
    public function scopeActive($query)
    {
        return $query->where('has_expired', false);
    }
}
