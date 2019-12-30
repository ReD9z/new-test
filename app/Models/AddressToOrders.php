<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AddressToOrders extends Model
{
    public function address() {
        return $this->belongsTo('App\Models\Address', 'address_id');
    }

    public function orders() {
        return $this->belongsTo('App\Models\Orders', 'order_id');
    }

    public function files() {
        return $this->belongsToMany('App\Models\Files', 'images_to_orders',  'address_to_orders_id' , 'files_id');
    }

    public static function saveTableFiles($files, $id)
    {
        DB::table('images_to_orders')->insert(['address_to_orders_id' => $id, 'files_id' => $files]);
    }

    public static function removeTableFiles($id)
    {
        DB::table('images_to_orders')->where('files_id', '=', $id)->delete();
    }

    public function statusAddress($id)
    {
        $entrances = Entrances::where('address_to_orders_id', $id)->get();
        $status = 0;
        foreach ($entrances as $key => $value) {
            if($value->status == 0) {
                $status = 0;
            }
            else if($value->status == 3)
                $status = 0;
            else {
                $status = 1;
            }
        }
        return $status;
    }
}
