<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entrances;

class Orders extends Model
{
    public function clients() {
        return $this->belongsTo('App\Models\Clients', 'clients_id');
    }

    public function orderAddress()
    {
        return $this->hasOne('App\Models\AddressToOrders', 'order_id', 'id');
    }

    public function orderManyAddress()
    {
        return $this->hasMany('App\Models\AddressToOrders', 'order_id', 'id');
    }

    public function tasks()
    {
        return $this->hasOne('App\Models\Tasks', 'orders_id', 'id');
    }

    public function getEntrances($id)
    {
        
        $torders = AddressToOrders::with('address', 'orders', 'files', 'entrances')->where('order_id', $id)->get();
        $number = 0;

        foreach ($torders as $key => $value) {
            $number = $number + count($value['entrances']);
        }
       
        return $number;
    }

    public function getEntrancesLoad($id)
    {
        $torders = AddressToOrders::with('address', 'orders', 'files', 'entrances')->where('order_id', $id)->pluck('id')->all();

        $entrances = Entrances::where([
            ['file_id', '!=', null],
            ['status', '=', 3]
        ])->whereIn('address_to_orders_id', $torders)->pluck('file_id')->all();

        $address = ImagesToOrders::with('orders')->whereIn('address_to_orders_id', $torders)->where([
            ['files_id', '!=', null],
        ])->pluck('files_id')->all();
        
        $result = array_merge($entrances, $address);
       
        return count($result);
    }
}
