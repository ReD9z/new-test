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
            if(!empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[4]) && !empty($row[5])) {
                $city = Address::getCityId($row[0]);
                $area = Address::getAreaId($row[1], $row[0]);
                $coordinates = Address::getCoordinates($row[0].", " .$row[2] .", ".$row[3]);

                Address::create([
                    'city_id' => $city,
                    'area_id' => $area,
                    'street' => $row[2],
                    'house_number' => $row[3],
                    'number_entrances' => $row[4],
                    'management_company' => $row[5],
                    'coordinates' => null
                ]);
            }
        }
    }

    public function batchSize(): int
    {
        return 5000;
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
