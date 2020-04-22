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
            'clients' => $this->clients ? $this->clients->users ? $this->clients->users->name : null : null,
            'city' => $this->clients ? $this->clients->cities ? $this->clients->cities->name : null : null,
            'manager_id' => $this->manager_id,
            'managers' => $this->managers ? $this->managers->users->name : null,
            'task_date_completion' => $this->task_date_completion ? date("d.m.Y", strtotime($this->task_date_completion)) : null,
            'comment' => $this->comment,
            'email' => $this->clients ? $this->clients->users ? $this->clients->users->email : null : null,
            'phone' => $this->clients ? $this->clients->users ? $this->clients->users->phone : null : null,
            'task_date_ended' => $this->TaskEndDate($this->client_id),
            'result' => $this->result,
            'created_at' => $this->clients ? date("d.m.Y", strtotime($this->clients->created_at)) : null,
            'status' => $this->status,
            'statusName' => $this->status == '1' ? 'В работе' : 'Завершена'
        ];
    }
}
