<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'title_ar'            => $this->title_ar,
            'title_en'            => $this->title_en,
            'description'         => app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en,
            'location'            => $this->location,

            // Pricing
            'start_price'         => $this->start_price,
            'current_price'       => $this->current_price,
            'min_bid_increment'   => $this->min_bid_increment,
            'buy_now_price'       => $this->buy_now_price,
            'reserve_met'         => $this->reserve_price
                ? $this->current_price >= $this->reserve_price
                : true,

            // Deposit
            'deposit_required'    => $this->deposit_required,
            'deposit_amount'      => $this->deposit_amount,

            // Timing
            'start_time'          => $this->start_time?->toISOString(),
            'end_time'            => $this->end_time?->toISOString(),
            'time_remaining'      => $this->time_remaining,
            'is_live'             => $this->is_live,

            // Status
            'status'              => $this->status,
            'is_featured'         => $this->is_featured,
            'bids_count'          => $this->bids_count,
            'views_count'         => $this->views_count,

            // Vehicle
            'vehicle'             => $this->whenLoaded('vehicle', fn () => [
                'id'              => $this->vehicle->id,
                'title'           => $this->vehicle->title,
                'make'            => $this->vehicle->make,
                'model'           => $this->vehicle->model,
                'year'            => $this->vehicle->year,
                'mileage'         => $this->vehicle->mileage,
                'color'           => $this->vehicle->color,
                'condition'       => $this->vehicle->condition,
                'fuel_type'       => $this->vehicle->fuel_type,
                'transmission'    => $this->vehicle->transmission,
                'primary_image_url'=> $this->vehicle->primary_image_url,
                'images'          => $this->vehicle->whenLoaded('images', fn () =>
                    $this->vehicle->images->map(fn ($img) => [
                        'id'  => $img->id,
                        'url' => $img->url,
                        'is_primary' => $img->is_primary,
                    ])
                ),
            ]),

            // Winner (if ended)
            'winner'              => $this->whenLoaded('winner', fn () => $this->winner ? [
                'id'   => $this->winner->id,
                'name' => $this->winner->full_name,
            ] : null),
            'winning_bid_amount'  => $this->winning_bid_amount,

            // Current user context
            'user_highest_bid'    => $this->when(
                isset($this->user_highest_bid),
                fn () => $this->user_highest_bid
            ),
            'is_watching'         => $this->when(
                isset($this->is_watching),
                fn () => $this->is_watching
            ),
            'has_deposited'       => $this->when(
                isset($this->has_deposited),
                fn () => $this->has_deposited
            ),

            'created_at'          => $this->created_at?->toISOString(),
        ];
    }
}
