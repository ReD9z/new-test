<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Managers extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'users_id' => $this->users_id,
            'name' => $this->users->name,
            'email' => $this->users->email,
            'phone' => $this->users->login,
            'login' => $this->users->phone,
            'city_id' => $this->city_id,
            'city' => $this->cities->name,
            'cityUser' => $this->cities,
            'moderator_id' => $this->moderator_id,
            'moderator' => $this->moderator->users->name
        ];
    }
}
