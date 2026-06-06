<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'first_name'          => $this->first_name,
            'last_name'           => $this->last_name,
            'full_name'           => $this->full_name,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'country_code'        => $this->country_code,
            'country'             => $this->country,
            'city'                => $this->city,
            'gender'              => $this->gender,
            'date_of_birth'       => $this->date_of_birth,
            'profile_photo_url'   => $this->profile_photo_url,
            'status'              => $this->status,
            'kyc_level'           => (int) $this->kyc_level,
            'email_verified'      => !is_null($this->email_verified_at),
            'identity_verified'   => !is_null($this->identity_verified_at),
            'roles'               => $this->getRoleNames()->toArray(),
            'wallet'              => $this->whenLoaded('wallet', fn () => new WalletResource($this->wallet)),
            'kyc_request'         => $this->whenLoaded('latestKycRequest', fn () => new KycRequestResource($this->latestKycRequest)),
            'created_at'          => $this->created_at?->toISOString(),
        ];
    }
}
