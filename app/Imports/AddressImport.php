<?php

namespace App\Imports;

use App\Models\Address;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


class AddressImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key=>$row) 
        {
            if($key != 0) {
                Address::create([
                    'city_id' => Address::getCityId($row[0]),
                    'area_id' => Address::getAreaId($row[1], $row[0]),
                    'street' => $row[2],
                    'house_number' => $row[3],
                    'number_entrances' => $row[4],
                    'management_company' => $row[5],
                    'coordinates' => Address::getCoordinates($row[0].", " .$row[2] .", ".$row[3])
                ]);
            }
        }
    }
}
