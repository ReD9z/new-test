<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    public function clients() {
        return $this->belongsTo('App\Models\Clients', 'clients_id');
    }

    public function orderAddress()
    {
        return $this->hasOne('App\Models\AddressToOrders', 'order_id', 'id');
    }

    public function tasks()
    {
        return $this->hasOne('App\Models\Tasks', 'orders_id', 'id');
    }
}
