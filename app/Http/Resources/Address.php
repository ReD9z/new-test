<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Address extends JsonResource
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
            'city' => $this->city,
            'area' => $this->area,
            'street' => $this->street,
            'house_number' => $this->house_number,
            'number_entrances' => $this->number_entrances
        ];
    }
}
