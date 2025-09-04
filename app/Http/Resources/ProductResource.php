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
        // $price = $this->price / 100; �������ó� ��� mutator, accessor ��� Model
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'brand' => $this->brand,
            'price' => $this->price,
            'price_formatted' => '$'. $this->price,
            'weight' => $this->weight,
            'description' => $this->description,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
