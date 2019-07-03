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
        return $this->hasMany('App\Models\AddressToOrders', 'order_id', 'id');
        // return $this->belongsToMany('App\Models\AddressToOrders', 'address',  'address_id');
        // return $this->belongsToMany('App\Models\AddressToOrders', 'address',  'address_id')->withPivot('address_id');
        // return $this->hasOne('App\Models\AddressToOrders');
        // return $this->hasManyThrough('App\Models\AddressToOrders');
        // return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id');
    }
}
