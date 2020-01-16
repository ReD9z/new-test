<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Clients extends JsonResource
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
            'users_id' => $this->users_id,
            'name' => $this->users ? $this->users->name : null,
            'email' => $this->users ? $this->users->email : null,
            'phone' => $this->users ? $this->users->phone : null,
            'login' => $this->users ? $this->users->login : null,
            'city' => $this->cities ? $this->cities->name : null,
            'city_id' => $this->city_id,
            'legal_name' => $this->legal_name,
            'actual_title' => $this->actual_title,
            'legal_address' => $this->legal_address,
            'actual_address' => $this->actual_address,
            'bank_name' => $this->bank_name,
            'bik' => $this->bik,
            'cor_score' => $this->cor_score,
            'settlement_account' => $this->settlement_account,
            'bank_name' => $this->bank_name,
            'files' => $this->files,
            'comments' => $this->comments
        ];
    }
}
