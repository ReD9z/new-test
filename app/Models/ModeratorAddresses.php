<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeratorAddresses extends Model
{
    public function city() {
        return $this->belongsTo('App\Models\CitiesToWorks', 'city_id');
    }
}
