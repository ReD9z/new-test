<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerTask extends Model
{
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
            }
        }

        return $mostRecent ? date("d.m.Y", $mostRecent) : null;
    }
}
