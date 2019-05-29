<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressToOrders extends Model
{
    public function address() {
        return $this->belongsTo('App\Models\Address', 'address_id');
    }

    public function orders() {
        return $this->belongsTo('App\Models\Orders', 'orders_id');
    }
}
