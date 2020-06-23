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
            'orderClient' => $this->clients ? 'Заказ #'.$this->id.' - '.$this->clients->users->name : null,
            'clients_name' => $this->clients ? $this->clients->users->name : null,
            'city_id' => $this->clients ? $this->clients->city_id : null,
            'city' => $this->clients ? $this->clients->cities->coordinates : null,
            'clients_id' => $this->clients_id,
            'client' => $this->clients,
            'task' => $this->tasks,
            'number_photos' => $this->number_photos,
            'address' => $this->orderAddress ? $this->orderAddress : null,
            'entrances' => $this->getEntrances($this->id),
            'entrances_load' => $this->getEntrancesLoad($this->id),
            'order_start_date' => date("d.m.Y", strtotime($this->order_start_date)),
            'order_end_date' => date("d.m.Y", strtotime($this->order_end_date))
        ];
    }
}
