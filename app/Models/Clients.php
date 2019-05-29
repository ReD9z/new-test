<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    public function cities() {
        return $this->belongsTo('App\Models\CitiesToWorks', 'city_id');
    }
}
