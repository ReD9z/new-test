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
}
