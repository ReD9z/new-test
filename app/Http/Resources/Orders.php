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
            'clients_id' => $this->clients_id,
            'task' => $this->tasks,
            'address' => $this->orderAddress ? $this->orderAddress : null,
            'order_start_date' => date("d.m.Y", strtotime($this->order_start_date)),
            'order_end_date' => date("d.m.Y", strtotime($this->order_end_date))
        ];
    }
}
