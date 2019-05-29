<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitiesToWorks extends Model
{
    public function clients()
    {
        return $this->belongsTo(Clients::class);
    }
}
