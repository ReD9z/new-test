<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Managers extends Model
{
    public function cities() {
        return $this->belongsTo('App\Models\CitiesToWorks', 'city_id');
    }

    public function users() {
        return $this->belongsTo('App\User', 'users_id');
    }

    public function moderator() {
        return $this->belongsTo('App\Models\Moderators', 'moderator_id');
    }
}
