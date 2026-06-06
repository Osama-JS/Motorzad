<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KycRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'full_name'       => $this->full_name,
            'country'         => $this->country,
            'id_number'       => $this->id_number,
            'id_image_url'    => $this->id_image ? asset('storage/' . $this->id_image) : null,
            'selfie_image_url'=> $this->selfie_image ? asset('storage/' . $this->selfie_image) : null,
            'status'          => $this->status,
            'status_label'    => $this->getStatusLabel(),
            'admin_note'      => $this->admin_note,
            'reviewed_at'     => $this->reviewed_at?->toISOString(),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }

    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending'  => __('Pending Review'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
            default    => $this->status,
        };
    }
}
