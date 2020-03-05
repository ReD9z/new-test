<?php

namespace App\Imports;

use App\Models\ManagerTask;
use App\User;
use App\Models\Clients;
use App\Models\CitiesToWorks;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ManagerTaskExport implements ToCollection, WithBatchInserts, WithChunkReading
{
    protected $user;

    function __construct($user) {
        $this->user = $user;
    }

    public function collection(Collection $rows)
    {   
        foreach ($rows as $key => $row) 
        {
            if($key != 0 && !empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[4])) {
                $cityName = $row[2];
                $nameOrganization = $row[3]; 
                $nameClient = $row[4]; 
                $phone = $row[5]; 
                $email = $row[6];

                $cityId = null;
                $client = null;
                $userId = null;


                if($nameOrganization) {
                    $userId = User::createUser($nameClient, $phone, $email);
                }
        
                if($cityName) {
                    $cityId = CitiesToWorks::createCity($cityName);
                }

                if($userId) {
                    $client = Clients::createClient($cityId, $userId, $nameOrganization);
                }

                ManagerTask::create([
                    'task_date_completion' => is_numeric($row[1]) ? gmdate("Y-m-d H:i:s", ($row[1] - 25569) * 86400) : null,
                    'client_id' => $client,
                    'manager_id' => $this->user,
                    'status' => $row[1] ? 2 : 1,
                    'comment' => $row[7]
                ]);
                
            }
        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000000000;
    }
}
