<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'city' => $this->city,
            'business_name' => $this->business_name,
            'avatar_path' => $this->avatar_path,
            'suspended_at' => $this->suspended_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

