<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Tasks extends JsonResource
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
            'orders_id' => $this->orders_id,
            'order_name' => 'Заказ #'.$this->orders_id.' - '.$this->orders->clients->users->name,
            'orderClient' => 'Заказ #'.$this->orders_id.' - '.$this->orders->clients->users->name,
            'orders' => $this->orders->clients->legal_name,
            'installer_id' => $this->installer_id,
            'installers' => $this->installers->users->name,
            'types' => $this->types->title,
            'types_to_works_id' => $this->types_to_works_id,
            'task_date_completion' => date("d-m-Y", strtotime($this->task_date_completion)),
            'comment' => $this->comment,
            'orderAddresses' => $this->orders->orderAddress,
            'status' => $this->status,
            'statusName' => $this->status == '1' ? 'В работе' : 'Завершена',
        ];
    }
}
