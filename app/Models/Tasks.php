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
        foreach ($item as $key => $value) {
            $address[] = "г." . $item[$key]['address']['cities']['name']
            . ", " . $item[$key]['address']['areas']['name'] . ","
            . " ул. " . $item[$key]['address']['street'] . ","
            . " Дом: " . $item[$key]['address']['house_number'] . ","
            . " Количество подъездов: " . $item[$key]['address']['number_entrances'] . ","
            . " Управляющая компания: " . $item[$key]['address']['management_company'];
        }

        return implode("\n", $address);
    }

    public function countAdresses($address)
    {
        $count = 0;
        foreach ($address as $key => $value) {
            $count = $count + $value->address->number_entrances;
        }
        return $count;
    }

    public function orderTask($id)
    {
        $order = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->where('id', $id)->get();
        return OrdersResource::collection($order);
    }
}
