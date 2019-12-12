<?php

namespace App\Models;
use App\Models\Entrances;
use App\Models\Areas;
use App\Models\CitiesToWorks;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Address extends Model
{
    protected $fillable = [
        'city_id',
        'area_id',
        'street',
        'house_number',
        'number_entrances',
        'management_company',
        'coordinates'
    ];

    public static function status($order)
    {
        $status = 'Свободен';
        if($order) {
            $todayDate = Carbon::createFromFormat('d.m.Y', Carbon::parse(Carbon::today())->format('d.m.Y'))->timestamp;
            foreach ($order as $value) {
                $itemDateStart = Carbon::createFromFormat('d.m.Y', Carbon::parse($value['orders']['order_start_date'])->format('d.m.Y'))->timestamp;
                $itemDateEnd = Carbon::createFromFormat('d.m.Y', Carbon::parse($value['orders']['order_end_date'])->format('d.m.Y'))->timestamp;

                if($todayDate >= $itemDateStart && $todayDate <= $itemDateEnd) {
                    $status = 'Занят';
                }
            }
        }
        
        return $status; 
    }

    public static function mb_ucfirst($word)
    {
        return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
    }

    public static function getCoordinates($value)
    {
        $api = new \Yandex\Geo\Api();
        $api->setQuery($value);
        $api->setLimit(1)->setLang(\Yandex\Geo\Api::LANG_RU)->load();
        $response = $api->getResponse();
        return $response->getLatitude().', '.$response->getLongitude();
    }

    public static function getCityId($value)
    {
        $id = null;
        $citywork = CitiesToWorks::where('name', self::mb_ucfirst($value))->first();
        if (!empty($citywork)) {
            $id = $citywork->id;
        } else {
            $toWorks = new CitiesToWorks;
            $toWorks->name = self::mb_ucfirst($value);
            $toWorks->save();
            $id = $toWorks->id;
        }
        return $id;
    }
    
    public static function getAreaId($value, $city)
    {
        $id = null;
        $citywork = CitiesToWorks::where('name', self::mb_ucfirst($city))->first();
        $city_id = null;
        if(!empty($citywork)) {
            $city_id = $citywork->id;
        }else {
            $toWorks = new CitiesToWorks;
            $toWorks->name = self::mb_ucfirst($city);
            $toWorks->save();
            $city_id = $toWorks->id;
        }
        
        $area = Areas::where('name', self::mb_ucfirst($value))->first();
        if (!empty($area)) {
            $areas_show = Areas::where([['name', '=', self::mb_ucfirst($value)],['city_id', '=', $city_id]])->first();
            if(!empty($areas_show)) {
                $id = $areas_show->id;
            } else {
                $areas = new Areas;
                $areas->name = self::mb_ucfirst($value);
                $areas->city_id = $city_id;
                $areas->save();
                $id = $areas->id;
            }
        } else {
            $areas = new Areas;
            $areas->name = self::mb_ucfirst($value);
            $areas->city_id = $city_id;
            $areas->save();
            $id = $areas->id;
        }
        return $id;
    }

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

    public function statusAddress($id)
    {
        $entrances = Entrances::with('address')->where('address_id', $id)->get();
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

    public function order()
    {
        return $this->hasOne('App\Models\AddressToOrders');
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
