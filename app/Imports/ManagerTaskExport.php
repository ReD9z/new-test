<?php

namespace App\Imports;

use App\Models\ManagerTask;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ManagerTaskExport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        dd($rows);
        foreach ($rows as $key=>$row) 
        {
            // if($key != 0) {

                // $item = ManagerTask::create([
                //     'task_date_completion' => '2019-09-13 10:29:26',
                //     'client_id' => ManagerTask::createClient($row[2], $row[3], $row[4], $row[5], $row[6]),
                //     'status' => 1,
                //     'comment' => $row[7]
                // ]);
            // }
        }
    }
}
