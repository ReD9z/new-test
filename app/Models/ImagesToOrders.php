<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagesToOrders extends Model
{

    public function orders() {
        return $this->belongsTo('App\Models\AddressToOrders', 'address_to_orders_id');
    }

}
