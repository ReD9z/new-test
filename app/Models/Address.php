<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function cities() {
        return $this->belongsTo('App\Models\CitiesToWorks', 'city_id');
    }

    public function areas() {
        return $this->belongsTo('App\Models\Areas', 'area_id');
    }

    public function orders() {
        return $this->hasMany('App\Models\Orders');
    }
    
    public function orderAddress()
    {
        return $this->hasOne('App\Models\AddressToOrders');
    }
}
