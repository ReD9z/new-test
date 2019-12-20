<?php

namespace App\Models;

use App\User;
use App\Models\Clients;
use App\Models\CitiesToWorks;
use Illuminate\Database\Eloquent\Model;

class ManagerTask extends Model
{
    protected $fillable = [
        'client_id',
        'status',
        'task_date_completion',
        'comment',
    ];

    public function clients() {
        return $this->belongsTo('App\Models\Clients', 'client_id');
    }

    public function managers() {
        return $this->belongsTo('App\Models\Managers', 'manager_id');
    }

    public function TaskEndDate($id)
    {
        $clients = ManagerTask::with('clients', 'managers.users', 'managers')->get()->where('client_id', $id);
        $mostRecent = null;

        foreach ($clients as $key => $value) {
            $curDate = strtotime($value->task_date_completion);
            if ($value->task_date_completion) {
                $mostRecent = $curDate;
            } else {
                $mostRecent = null;
            }
        }

        return $mostRecent ? date("d.m.Y", $mostRecent) : null;
    }

    function mb_ucfirst($word)
    {
        return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
    }

    public static function createClient($city, $nameOrganization, $nameClient, $phone, $email)
    {
        
        $users = User::where('name', mb_ucfirst(array_values($nameClient)->first();
        $citywork = CitiesToWorks::where('name', mb_ucfirst(array_values($city))->first();
        $clients = Clients::with('cities', 'users', 'comments')->where('users_id', $users_id)->first();
        
        if(!empty($clients)) {
            $toClients = $clients->id;
        }else {
            $clients = new Clients;
            if (!empty($users)) { 
                $clients->users_id = $users->id;
            } else {
                $toUsers = new User;
                $toUsers->name = $nameClient;
                $toUsers->email = $email;
                $toUsers->phone = $phone;
                $toUsers->login = null;
                $toUsers->role = 'client';
                
                $toUsers->password = null;
                
                $token = $toUsers->createToken('Laravel Password Grant Client')->accessToken;
                $toUsers->save();
                $clients->users_id = $toUsers->id;
            }

            if (!empty($citywork)) {
                $clients->city_id = $citywork->id;
            } else {
                $toWorks = new CitiesToWorks;
                $toWorks->name = mb_ucfirst(array_values($request->input())[2]);
                $toWorks->save();
                $clients->city_id = $toWorks->id;
            }

            $clients->legal_name = mb_ucfirst($nameOrganization);
            $clients->actual_title = mb_ucfirst($nameOrganization);
           
            $clients->save();
            $toClients = $clients->id;
        }
    }
}
