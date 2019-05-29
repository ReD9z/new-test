<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Installers extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->users->name,
            'email' => $this->users->email,
            'password' => $this->users->password,
            'city_id' => $this->city_id,
            'city' => $this->address->city
        ];
    }
}
