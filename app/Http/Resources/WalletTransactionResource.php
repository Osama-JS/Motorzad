<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'type'           => $this->type,             // 'credit' | 'debit'
            'type_label'     => $this->type === 'credit' ? __('Deposit') : __('Withdrawal'),
            'amount'         => (float) $this->amount,
            'sign'           => $this->type === 'credit' ? '+' : '-',
            'description'    => $this->description,
            'payment_method' => $this->payment_method,
            'maturity_time'  => $this->maturity_time?->toISOString(),
            'attachment_url' => $this->attachment_path
                ? asset('storage/' . $this->attachment_path)
                : null,
            'created_by'     => $this->whenLoaded('creator', fn () => [
                'id'   => $this->creator->id,
                'name' => $this->creator->full_name,
            ]),
            'created_at'     => $this->created_at?->toISOString(),
        ];
    }
}
