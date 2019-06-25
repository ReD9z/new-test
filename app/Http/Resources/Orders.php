<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Orders extends JsonResource
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
            'actual_title' => $this->clients->actual_title,
            'clients_id' => $this->clients_id,
            'address' => [],
            'order_start_date' => date("Y-m-d", strtotime($this->order_start_date)),
            'order_end_date' => date("Y-m-d", strtotime($this->order_end_date))
        ];
    }
}
