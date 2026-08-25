<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'make_ar'             => $this->make_ar,
            'make_en'             => $this->make_en,
            'model_ar'            => $this->model_ar,
            'model_en'            => $this->model_en,
            'year'                => $this->year,
            'color_ar'            => $this->color_ar,
            'color_en'            => $this->color_en,
            'vin_number'          => $this->vin_number,
            'mileage'             => $this->mileage,
            'fuel_type'           => $this->fuel_type,
            'transmission'        => $this->transmission,
            'engine_capacity'     => $this->engine_capacity,
            'cylinders'           => $this->cylinders,
            'condition'           => $this->condition,
            'description_ar'      => $this->description_ar,
            'description_en'      => $this->description_en,
            'status'              => $this->status,
            'damage_points'       => $this->damage_points,
            'primary_image_url'   => $this->primary_image_url,
            'images'              => $this->whenLoaded('images', fn () => 
                $this->images->map(fn ($img) => [
                    'id'         => $img->id,
                    'url'        => $img->url,
                    'is_primary' => $img->is_primary,
                    'sort_order' => $img->sort_order,
                ])
            ),
            'is_published_for_auction' => $this->relationLoaded('auction') ? $this->auction !== null : $this->auction()->exists(),
            'auction'             => $this->whenLoaded('auction', fn () => new AuctionResource($this->auction)),
            'created_at'          => $this->created_at?->toISOString(),
            'updated_at'          => $this->updated_at?->toISOString(),
        ];
    }
}
