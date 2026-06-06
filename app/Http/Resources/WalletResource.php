<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'balance'           => (float) $this->balance,
            'total_deposits'    => (float) $this->total_deposits,
            'total_withdrawals' => (float) $this->total_withdrawals,
            'debt_ceiling'      => (float) $this->debt_ceiling,
            'debt_usage'        => (float) $this->debt_usage,
            'currency'          => 'SAR',
            'user'              => $this->whenLoaded('user', fn () => new UserResource($this->user)),
        ];
    }
}
