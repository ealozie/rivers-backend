<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
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
            'subject' => $this->subject,
            'note' => $this->note,
            'type' => $this->noteable_type,
            'entity' => $this->noteable,
            //'added_by' => new IndividualResource($this->user()),
            'added_by' => new UserResource($this->admin),
            'created_at' => $this->created_at,
        ];
    }
}
