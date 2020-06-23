<?php

namespace App\Imports;

use App\Models\Address;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AddressImport implements ToCollection, WithBatchInserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) 
        {
            $city = null;
            $area = null;
            $cityA = !empty($row[0]) ? $row[0] : null;
            $areaB = !empty($row[1]) ? $row[1] : null; 
            $street = !empty($row[2]) ? $row[2] : null;
            $house_number = !empty($row[3]) ? $row[3] : null;
            $number_entrances = !empty($row[4]) ? $row[4] : null;
            $management_company = !empty($row[5]) ? $row[5] : null;

            if($cityA) {
                $city = Address::getCityId($cityA);
            }
            if($areaB) {
                $area = Address::getAreaId($areaB, $cityA);
            }

            Address::create([
                'city_id' => $city,
                'area_id' => $area,
                'street' =>  $street,
                'house_number' => $house_number,
                'number_entrances' => $number_entrances,
                'management_company' => $management_company
            ]);
        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 100000;
    }
}
