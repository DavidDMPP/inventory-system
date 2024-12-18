<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'description'
    ];

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }
}