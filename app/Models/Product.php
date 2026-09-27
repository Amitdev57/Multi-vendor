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
        $mediaUrl = $this->getFirstMediaUrl('images');

        if ($mediaUrl && filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
            return $mediaUrl;
        }

        if ($mediaUrl) {
            $relativePath = parse_url($mediaUrl, PHP_URL_PATH);
            if ($relativePath) {
                $publicPath = public_path(ltrim($relativePath, '/'));
                if (file_exists($publicPath)) {
                    return $mediaUrl;
                }
            }
        }

        if (! empty($this->image_url) && filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }

        return 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80';
    }
}
