<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $authUser = Auth::user();
        
        $userData = [
            'Id' => $this->id,
            'Name' => $this->name,
            'Email' => $this->email,
            'Password' => $this->password,
            'Role' => $this->role,
        ];

        // Include tasks only if the authenticated user is an admin
        if ($authUser->role === 'admin') {
            $userData['tasks'] = $this->whenLoaded('tasks');
        }

        return $userData;
    }
}
