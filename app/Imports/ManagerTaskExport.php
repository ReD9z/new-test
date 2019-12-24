<?php

namespace App\Imports;

use App\Models\ManagerTask;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ToModel;

class ManagerTaskExport implements ToModel, WithBatchInserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    // public function collection(Collection $rows)
    // {   
    //     foreach ($rows as $key => $row) 
    //     {
    //         if($key != 0) {
    //             $item = ManagerTask::create([
    //                 'task_date_completion' => '2019-09-13 10:29:26',
    //                 'client_id' => ManagerTask::createClient($row[2], $row[3], $row[4], $row[5], $row[6]),
    //                 'status' => 1,
    //                 'comment' => $row[7]
    //             ]);
    //         }
    //     }
    // }

    public function model(array $row)
    {
        if($row[0] != null) {
            return new ManagerTask([
                'task_date_completion' => '2019-09-13 10:29:26',
                'client_id' => ManagerTask::createClient($row[2], $row[3], $row[4], $row[5], $row[6]),
                'status' => 1,
                'comment' => $row[7]
            ]);
        }
        
        // echo "<pre>";
        // var_dump($row);
        // echo "</pre>";
         
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 10000000000;
    }
}
