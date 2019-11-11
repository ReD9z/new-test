<?php

namespace App\Models;
use App\Models\Entrances;
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

    public function entrances()
    {
        return $this->hasMany('App\Models\Entrances', 'address_id', 'id');
    }
    
    public static function addEntrances($data) {
        $number = (int)$data->number_entrances;
        $id = $data->id;
        for($i=0; $i < $number; $i++) {
            $entrances = new Entrances;
            $entrances->address_id = $id;
            $entrances->save();
        }
    }
}
