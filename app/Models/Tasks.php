<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    public function orders() {
        return $this->belongsTo('App\Models\Orders', 'orders_id');
    }

    public function installers() {
        return $this->belongsTo('App\Models\Installers', 'installer_id');
    }

    public function types() {
        return $this->belongsTo('App\Models\TypesToWorks', 'types_to_works_id');
    }
}
