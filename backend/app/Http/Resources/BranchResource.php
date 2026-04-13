<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'name' => $this->name,
            'corporate_name' => $this->corporate_name,
            'slug_name' => $this->slug_name,
            'document' => $this->document,
            'state_registration' => $this->state_registration,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => [
                'zip_code' => $this->zip_code,
                'street' => $this->street,
                'number' => $this->number,
                'complement' => $this->complement,
                'district' => $this->district,
                'city' => $this->city,
                'state' => $this->state,
            ],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
