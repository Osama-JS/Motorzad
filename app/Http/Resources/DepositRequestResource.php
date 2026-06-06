<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'amount'          => (float) $this->amount,
            'status'          => $this->status,
            'status_label'    => $this->getStatusLabel(),
            'admin_note'      => $this->admin_note,
            'receipt_url'     => $this->receipt_path
                ? asset('storage/' . $this->receipt_path)
                : null,
            'bank_account'    => $this->whenLoaded('bankAccount', fn () => [
                'id'               => $this->bankAccount->id,
                'bank_name'        => $this->bankAccount->bank_name,
                'iban'             => $this->bankAccount->iban,
                'beneficiary_name' => $this->bankAccount->beneficiary_name,
            ]),
            'processed_at'    => $this->processed_at?->toISOString(),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }

    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending'  => __('Pending'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
            default    => $this->status,
        };
    }
}
