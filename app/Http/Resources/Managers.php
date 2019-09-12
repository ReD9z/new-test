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
            'name' => $this->users ? $this->users->name : null,
            'email' => $this->users ? $this->users->email : null,
            'phone' => $this->users ? $this->users->login : null,
            'login' => $this->users ? $this->users->phone : null,
            'city_id' => $this->city_id,
            'city' => $this->cities ? $this->cities->name : null,
            'cityUser' => $this->cities,
            'moderator_id' => $this->moderator_id,
            'moderator' => $this->moderator ? $this->moderator->users->name : null
        ];
    }
}
