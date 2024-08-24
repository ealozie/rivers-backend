<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountManagerResource extends JsonResource
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
            'manager' => new UserResource($this->manager),
            'entity_type' => $this->accountable_type,
            'entity' => $this->accountable,
            'created_at' => $this->created_at,
        ];
    }
}
