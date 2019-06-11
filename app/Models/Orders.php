<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    public function clients() {
        return $this->belongsTo('App\Models\Clients', 'clients_id');
    }
}
