<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency_code' => $this->currency_code,
            'stock' => $this->stock,
            'image_url' => $this->display_image,
            'category' => $this->whenLoaded('category', fn () => $this->category?->name),
            'vendor' => $this->whenLoaded('vendor', fn () => $this->vendor?->business_name),
        ];
    }
}
