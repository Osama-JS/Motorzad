<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'requested_amount' => (float) $this->requested_amount,
            'approved_amount'  => $this->approved_amount ? (float) $this->approved_amount : null,
            'status'           => $this->status,
            'status_label'     => $this->getStatusLabel(),
            'payment_method'   => $this->payment_method,
            'admin_notes'      => $this->admin_notes,
            'processed_at'     => $this->processed_at?->toISOString(),
            'created_at'       => $this->created_at?->toISOString(),
        ];
    }

    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending'    => __('Pending'),
            'processing' => __('Processing'),
            'approved'   => __('Approved'),
            'rejected'   => __('Rejected'),
            'completed'  => __('Completed'),
            default      => $this->status,
        };
    }
}
