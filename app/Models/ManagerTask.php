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
}