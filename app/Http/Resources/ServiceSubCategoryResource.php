<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceSubCategoryResource extends JsonResource
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
            'fees' => $this->fees,
            'description' => $this->description,
            'processing_time' => $this->processing_time,
            'status' => $this->status,
            'landing_page_url' => $this->landing_page_url,
            'created_at' => $this->created_at,
            'service_category' => $this->service_category,
            'service_provider' => $this->service_provider,
        ];
    }
}
