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
    
    public function orderAddress()
    {
        return $this->hasMany('App\Models\AddressToOrders', 'address_id', 'id');
        // return $this->belongsToMany('App\Models\AddressToOrders', 'address',  'address_id');
        // return $this->belongsToMany('App\Models\AddressToOrders', 'address',  'address_id')->withPivot('address_id');
        // return $this->hasOne('App\Models\AddressToOrders');
        // return $this->hasManyThrough('App\Models\AddressToOrders');
        // return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id');
    }
}
