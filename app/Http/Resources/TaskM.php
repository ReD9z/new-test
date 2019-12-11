<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskM extends JsonResource
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
            'task_name' => 'Задача #'.$this->id,
            'installers' => $this->installers ? $this->installers->users->name : null,
            'installer_id' => $this->installer_id,
            'types_id' => $this->types_to_works_id,
            'types' => $this->types ? $this->types->title : null,
            'task_date_completion' => date("d.m.Y", strtotime($this->task_date_completion)),
            'number_addresses' => $this->orders ? count($this->orders->orderAddress) : null,
            'number_entrances' => $this->orders ? $this->countAdresses($this->orders->orderAddress) : null,
            'comment' => $this->comment
        ];
    }
}
