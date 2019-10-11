<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    public function comments() {
        // return $this->belongsTo('App\Models\CommentToFiles', 'files_id');
         return $this->hasMany('App\Models\CommentToFiles', 'files_id', 'id');
    }
}
