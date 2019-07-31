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
            'orders' => $this->orders->clients->legal_name,
            'installer_id' => $this->installer_id,
            'installers' => $this->installers->users->name,
            'types' => $this->types->title,
            'types_to_works_id' => $this->types_to_works_id,
            'task_date_completion' => date("d-m-Y", strtotime($this->task_date_completion)),
            'comment' => $this->comment,
        ];
    }
}
