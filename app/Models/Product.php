<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'price',
        'stock',
        'status',
        'product_image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'boolean',
    ];

    // Relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
