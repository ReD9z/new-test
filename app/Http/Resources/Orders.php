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
            'clients' => $this->clients->actual_title,
            'client_id' => $this->client_id,
            'order_start_date' => $this->order_start_date,
            'order_end_date' => $this->order_end_date
        ];
    }
}
