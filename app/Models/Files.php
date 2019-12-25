<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    public function comments() {
        return $this->hasMany('App\Models\CommentToFiles', 'files_id', 'id');
    }

    public function address() {
        return $this->hasMany('App\Models\ImagesToOrders', 'files_id', 'id');
    }

    public function entrances() {
        return $this->hasOne('App\Models\Entrances', 'file_id', 'id');
    }
}
