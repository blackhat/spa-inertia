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
        $price = $this->price / 100;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'brand' => $this->brand,
            'price' => $price,
            'price_formatted' => '$'.$price,
            'weight' => $this->weight,
            'description' => $this->description,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
