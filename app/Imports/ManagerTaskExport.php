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
use Illuminate\Support\Facades\Auth;

class ManagerTaskExport implements ToCollection, WithBatchInserts, WithChunkReading
{
 
    public function collection(Collection $rows)
    {   
        $manager = null;
        $moderator = null;

        if(Auth::user()->managers) {
            $manager = Auth::user()->managers->id;
            $moderator = Auth::user()->managers->moderator_id;
        }
        if(Auth::user()->moderators) {
            $moderator = Auth::user()->moderators->id;
        }
        foreach ($rows as $key => $row) 
        {
            if($key != 0 && !empty($row[0]) && (!empty($row[7]) || !empty($row[5]))) {
                $cityName = !empty($row[2]) ? $row[2] : null;
                $nameOrganization = !empty($row[3]) ? $row[3] : null; 
                $nameClient = !empty($row[4]) ? $row[4] : null; 
                $phone = !empty($row[5]) ? $row[5] : null; 
                $email = !empty($row[6]) ? $row[6] : null;
                $date = !empty($row[1]) ? $row[1] : null;
                $comment = !empty($row[7]) ? $row[7] : null;

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
                    $client = Clients::createClient($cityId, $userId, $nameOrganization, $manager, $moderator);
                }

                ManagerTask::create([
                    'task_date_completion' => is_numeric($date) ? gmdate("Y-m-d H:i:s", ($date - 25569) * 86400) : null,
                    'client_id' => $client,
                    'manager_id' => $manager,
                    'status' =>  $date ? 2 : 1,
                    'comment' => $comment
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
