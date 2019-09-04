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
            'order_name' => 'Заказ #'.$this->id,
            'orderClient' => 'Заказ #'.$this->id.' - '.$this->clients->users->name,
            'clients_name' => $this->clients->users->name,
            'clients_id' => $this->clients_id,
            'address' => $this->orderAddress ? $this->orderAddress : [],
            'order_start_date' => date("d.m.Y", strtotime($this->order_start_date)),
            'order_end_date' => date("d.m.Y", strtotime($this->order_end_date))
        ];
    }
}
