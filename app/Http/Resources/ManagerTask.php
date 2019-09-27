<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ManagerTask extends JsonResource
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
            'client_id' => $this->client_id,
            'client' => $this->clients ? $this->clients->users->name : null,
            'manager_id' => $this->manager_id,
            'managers' => $this->managers ? $this->managers->users->name : null,
            'task_date_completion' => date("d.m.Y", strtotime($this->task_date_completion)),
            'comment' => $this->comment,
            'status' => $this->status,
            'result' => $this->result,
            'statusName' => $this->status == '1' ? 'В работе' : 'Завершена'
        ];
    }
}
