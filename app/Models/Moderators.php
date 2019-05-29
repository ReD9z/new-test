<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moderators extends Model
{
    public function users() {
        return $this->belongsTo('App\User', 'users_id');
    }
}
