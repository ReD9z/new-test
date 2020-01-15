<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressToOrdersTasks extends JsonResource
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
            'city_id' => $this->address->city_id,
            'city' => $this->address->cities ? $this->address->cities->name : null,
            'area' =>  $this->address->areas ? $this->address->areas->name : null,
            'area_id' => $this->address->area_id,
            'street' => $this->address->street,
            'house_number' => $this->address->house_number,
            'number_entrances' => $this->address->number_entrances,
            'management_company' => $this->address->management_company,
            'status' => $this->statusAddress($this->id),
            'coordinates' => $this->coordinates
        ];
    }
}
