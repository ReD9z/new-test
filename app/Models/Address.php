<?php

namespace App\Models;
use App\Models\Entrances;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'street'
    ];

    public function cities() {
        return $this->belongsTo('App\Models\CitiesToWorks', 'city_id');
    }

    public function areas() {
        return $this->belongsTo('App\Models\Areas', 'area_id');
    }
    
    public function orderAddress()
    {
        return $this->hasMany('App\Models\AddressToOrders', 'address_id', 'id');
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

    public static function editEntrances($data) {
        $arr = Entrances::where('address_id', $data->id)->get();
        $absNumber = abs((int)$data->number_entrances - count($arr));
        $number = (int)$data->number_entrances;
        $id = $data->id;
        if(count($arr ) == 0) {
            for($i=0; $i < $number; $i++) {
                $entrances = new Entrances;
                $entrances->number = $i+1;
                $entrances->address_id = $id;
                $entrances->save();
            }
        }
        else if((int)$data->number_entrances < count($arr)) {
            for($i=0; $i < $absNumber; $i++) {
                $num = Entrances::where('address_id', $data->id)->get();
                $address = $num[count($num)-1];
                $address->delete();
            }
        }

        else if((int)$data->number_entrances > count($arr)) {
           for($i=0; $i < $absNumber; $i++) {
                $entrances = new Entrances;
                $entrances->number = count($arr) + ($i+1);
                $entrances->address_id = $id;
                $entrances->save();
            }
        }
    }
}
