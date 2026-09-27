<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['vendor_id', 'category_id', 'name', 'slug', 'description', 'price', 'currency_code', 'stock', 'status', 'image_url'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'stock' => 'integer'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getDisplayImageAttribute(): string
    {
        return $this->getFirstMediaUrl('images') ?: ($this->image_url ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80');
    }
}
