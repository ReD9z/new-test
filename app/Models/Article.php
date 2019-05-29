<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    public function files() {
        return $this->belongsToMany('App\Models\Files', 'article_to_files',  'articles_id' , 'files_id');
    }

    public static function saveTableFiles($files, $id)
    {
        DB::table('article_to_files')->insert(['articles_id' => $id, 'files_id' => $files]);
    }

    public static function removeTableFiles($id)
    {
        DB::table('article_to_files')->where('files_id', '=', $id)->delete();
    }
}
