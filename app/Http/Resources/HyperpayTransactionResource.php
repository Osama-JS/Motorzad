<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HyperpayTransactionResource extends JsonResource
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
            'merchant_transaction_id' => $this->merchant_transaction_id,
            'checkout_id' => $this->checkout_id,
            'hyperpay_payment_id' => $this->hyperpay_payment_id,
            'brand' => $this->brand,
            'brand_name' => $this->brand_name, // from accessor
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'result_code' => $this->result_code,
            'result_description' => $this->result_description,
            'card_masked' => $this->masked_card, // from accessor
            'card_holder' => $this->card_holder,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
