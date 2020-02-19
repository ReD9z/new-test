<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitiesToWorks extends Model
{
    protected $fillable = [
        'id',
        'name'
    ];
     public static function mb_ucfirst($word)
    {
        return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
    }
    public static function createCity($cityName)
    {
        $cityId = null;
        $citywork = CitiesToWorks::where('name', self::mb_ucfirst($cityName))->first();
        if(!$citywork) {
            $cities = CitiesToWorks::create([
                'name' => $cityName ? self::mb_ucfirst($cityName) : null,
            ]);
            $cityId = $cities->id;
        } else {
            $cityId = $citywork->id;
        }
        return $cityId;
    }
}
