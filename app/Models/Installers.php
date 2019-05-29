<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installers extends Model
{
    public function address() {
        return $this->belongsTo('App\Models\Address', 'city_id');
    }

    public function users() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
