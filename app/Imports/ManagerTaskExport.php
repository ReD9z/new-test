<?php

namespace App\Imports;

use App\Models\ManagerTask;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ManagerTaskExport implements ToCollection, WithBatchInserts, WithChunkReading, WithColumnFormatting
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
            if($key != 0 && $row[0] != null) {
                $item = ManagerTask::create([
                    'task_date_completion' => is_numeric($row[1]) ? gmdate("Y-m-d H:i:s", ($row[1] - 25569) * 86400) : null,
                    'client_id' => ManagerTask::createClient($row[2], $row[3], $row[4], $row[5], $row[6]),
                    'status' => $row[1] ? 2 : 1,
                    'comment' => $row[7]
                ]);
            }
        }
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'C' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
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
