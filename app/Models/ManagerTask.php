<?php

namespace App\Models;

use App\User;
use App\Models\Clients;
use App\Models\CitiesToWorks;
use Illuminate\Database\Eloquent\Model;

class ManagerTask extends Model
{
    protected $fillable = [
        'id',
        'client_id',
        'manager_id',
        'status',
        'task_date_completion',
        'comment',
        'result'
    ];

    public function clients() {
        return $this->belongsTo('App\Models\Clients', 'client_id');
    }

    public function managers() {
        return $this->belongsTo('App\Models\Managers', 'manager_id');
    }

    public function TaskEndDate($id)
    {
        $clients = ManagerTask::where('client_id', $id)->first();
        $mostRecent = null;
        if($clients) {
            $curDate = strtotime($clients->task_date_completion);
            $mostRecent = $curDate;
        } else {
            $mostRecent = null;
        }

        return $mostRecent ? date("d.m.Y", $mostRecent) : null;
    }

     public static function mb_ucfirst($word)
    {
        return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
    }


    public static function createClient($city, $nameOrganization, $nameClient, $phone, $email)
    {
        $users = User::where('email', self::mb_ucfirst($email))->first();
        $toUsersId = null;
        
        if($users) {
            $toUsersId = $users->id;
        }else {
            $toUsers = new User;
            $toUsers->name = $nameClient;
            $toUsers->email = $email;
            $toUsers->phone = $phone;
            $toUsers->login = null;
            $toUsers->role = 'client';
            $toUsers->password = null;
            $token = $toUsers->createToken('Laravel Password Grant Client')->accessToken;
            $toUsers->save();
            $toUsersId = $toUsers->id;
        }

        $citywork = CitiesToWorks::where('name', self::mb_ucfirst($city))->first();
        $toCityId = null;
        if($citywork) {
            $toCityId = $citywork->id;
        } else {
            $toWorks = new CitiesToWorks;
            $toWorks->name = self::mb_ucfirst($city);
            $toWorks->save();
            $toCityId = $toWorks->id;
        }

        $toClientID = null;

        $clients = Clients::with('cities', 'users', 'comments')->where('users_id', $toUsersId)->first();
        if($clients) {
            $toClientID = $clients->id;
        } else {
            $clientsNew = new Clients;
            $clientsNew->users_id = $toUsersId;
            $clientsNew->city_id = $toCityId;
            $clientsNew->legal_name = self::mb_ucfirst($nameOrganization);
            $clientsNew->actual_title = self::mb_ucfirst($nameOrganization);
            $clientsNew->save();
            $toClientID = $clientsNew->id;
        }

        return $toClientID;
    }
}
