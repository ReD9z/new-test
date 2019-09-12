<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressToOrders extends JsonResource
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
            'order_id' => $this->order_id,
            'address_id' => $this->address_id,
            'address' => $this->address->cities ? $this->address->cities->name : null,
            'orders' => $this->orders->clients ? $this->orders->clients->actual_title : null,
            'files' => $this->files
        ];
    }
}
