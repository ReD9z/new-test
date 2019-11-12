<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Address;

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

    public function countAdresses($address)
    {
        $count = 0;
        foreach ($address as $key => $value) {
            $count = $count + $value->address->number_entrances;
        }
        return $count;
    }
}
