<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Address;
use App\Http\Resources\Orders as OrdersResource;
use App\Models\Orders;

class Tasks extends Model
{
    public function orders() {
        return $this->belongsTo('App\Models\Orders', 'orders_id');
    }

    public function installers() {
        return $this->belongsTo('App\Models\Installers', 'installer_id');
    }

    public function types() {
        return $this->belongsTo('App\Models\TypesToWorks', 'types_to_works_id');
    }

    public function getAddress($item)
    {
        $address = [];
        if($item) {
            foreach ($item as $key => $value) {
                $address[] = "г." . $item[$key]['address']['cities']['name']
                . ", " . $item[$key]['address']['areas']['name'] . ","
                . " ул. " . $item[$key]['address']['street'] . ","
                . " Дом: " . $item[$key]['address']['house_number'] . ","
                . " Количество подъездов: " . $item[$key]['address']['number_entrances'] . ","
                . " Управляющая компания: " . $item[$key]['address']['management_company'];
            }
            return implode("\n", $address);
        }else {
            return null;
        }

      
    }

    public function countEntrances($id)
    {
        $torders = AddressToOrders::with('address', 'orders', 'files', 'entrances')->where('order_id', $id)->pluck('id')->all();
     
        $entrances = Entrances::with('address')->where('status', '!=', 3)->whereIn('address_to_orders_id', $torders)->get();
        
        return count($entrances);
    }
    
    public function countAdresses($id)
    {
        $torders = AddressToOrders::where('order_id', $id)->with(['entrances' => function ($query) {
                $query->where('status', '!=', 3);
        }, 'address', 'orders', 'files'])->get();
        
        $collection = collect($torders);
        $collection = $collection->filter(function($value) {
            if(count($value['entrances']) > 0) {
                return $value;
            };
        });
        
        return count($collection);
    }


    public function orderTask($id)
    {
        $order = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->where('id', $id)->get();
        return OrdersResource::collection($order);
    }
}
