<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOut extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'selling_price',
        'date',
        'invoice_number',
        'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}