<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'unique_id' => $this->unique_id,
            'phone_number' => $this->phone_number,
            'last_login_at' => $this->last_login_at,
            'mda_biller_id' => $this->mda_biller_id,
            'residential_address' => $this->residential_address,
            'roles' => $this->get_roles_as_array_list(),
            'local_government_area' => $this->local_government_area_id ? $this->local_government_area : null,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }


    public function get_roles_as_array_list()
    {
        $roles = $this->roles;
        $roles_array = [];
        foreach ($roles as $role) {
            $roles_array[] = $role->name;
        }
        return $roles_array;
    }
}
