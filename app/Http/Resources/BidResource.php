<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BidResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'amount'       => $this->amount,
            'status'       => $this->status,
            'is_auto_bid'  => $this->is_auto_bid,
            'max_auto_bid' => $this->when(
                $this->user_id === auth('sanctum')->id(), // Only show max_auto_bid to the bid owner
                $this->max_auto_bid
            ),
            'bidder'       => $this->whenLoaded('user', fn () => [
                'id'       => $this->user->id,
                'name'     => $this->user->full_name,
                'photo'    => $this->user->profile_photo_url,
            ]),
            'auction'      => $this->whenLoaded('auction', fn () => [
                'id'            => $this->auction->id,
                'title'         => $this->auction->title,
                'current_price' => $this->auction->current_price,
                'status'        => $this->auction->status,
                'end_time'      => $this->auction->end_time?->toISOString(),
                'image_url'     => clone $this->auction->vehicle?->primary_image_url ?? null,
            ]),
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
