<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Moderators extends JsonResource
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
            'phone' => $this->users ? $this->users->phone : null,
            'login' => $this->users ? $this->users->login : null,
            'city_id' => $this->city_id,
            'city' => $this->users ? $this->cities->name : null
        ];
    }
}
