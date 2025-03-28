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
            'orders' =>  $this->orders->clients ? 'Заказ #'.$this->orders->id.' - '.$this->orders->clients->users->name : null,
            'orderAddress' => $this->getAddress($this->orders->orderAddress),
            'order' => $this->orders ? $this->orderTask($this->orders->id)[0] : null,
            'installer_id' => $this->installer_id,
            'installers' => $this->installers ? $this->installers->users->name : null,
            'types' => $this->types ? $this->types->title : null,
            'types_to_works_id' => $this->types_to_works_id,
            'task_date_completion' => date("d.m.Y", strtotime($this->task_date_completion)),
            'comment' => $this->comment,
            'status' => $this->status,
            'photo_date' => $this->photo_date == 1 ? $this->photo_date : 2,
            'photoDate' => $this->photo_date == 1 ? 'Да' : 'Нет',
            'statusName' => $this->status == '1' ? 'В работе' : 'Завершена',
        ];
    }
}
