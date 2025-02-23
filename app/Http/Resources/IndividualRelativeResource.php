<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndividualRelativeResource extends JsonResource
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
            'relationship' => $this->relationship,
            'individual' => new IndividualResource($this->individual),
            'relative' => new IndividualResource($this->relative),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
