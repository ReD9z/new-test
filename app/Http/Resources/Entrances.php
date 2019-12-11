<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Entrances extends JsonResource
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
            'address_id' => $this->address_id,
            'house_number' => $this->address->house_number,
            'street' => $this->address->street,
            'file_id' => $this->file_id,
            'urlFile' => $this->files ? $this->files->url : null,
            'number' => $this->number,
            'shield' => $this->shield,
            'glass' => $this->glass,
            'information' => $this->information,
            'mood' => $this->mood,
            'status' => $this->status,
            'coordinates' => $this->address->coordinates
        ];
    }
}
