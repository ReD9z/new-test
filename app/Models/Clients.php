<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clients extends Model
{
    public function cities() {
        return $this->belongsTo('App\Models\CitiesToWorks', 'city_id');
    }

    public function users() {
        return $this->belongsTo('App\User', 'users_id');
    }

    public function files() {
        return $this->belongsToMany('App\Models\Files', 'client_to_files',  'client_id' , 'files_id');
    }

    public function comments() {
        return $this->hasMany('App\Models\CommentToFiles', 'client_id', 'id');
    }

    // public function comments() {
    //     return $this->belongsToMany('App\Models\CommentToFiles', 'comment_to_files',  'client_id' , 'files_id');
    // }

    // public function comments() {
    //     return $this->hasMany('App\Models\CommentToFiles', 'files_id', 'id');
    // }

    public static function saveTableFiles($files, $id, $comment)
    {
        DB::table('client_to_files')->insert(['client_id' => $id, 'files_id' => $files]);
        DB::table('comment_to_files')->insert(['client_id' => $id, 'files_id' => $files, 'comment' => $comment]);
    }

    public static function removeTableFiles($id)
    {
        DB::table('client_to_files')->where('files_id', '=', $id)->delete();
    }

    public static function saveComment($fileId, $idClient, $comment)
    {
        DB::table('comment_to_files')->insert(['client_id' => $idClient, 'files_id' => $fileId, 'comment' => $comment]);
    }

    public static function removeComment($id)
    {
        DB::table('comment_to_files')->where('id', '=', $id)->delete();
    }
    
    public static function editComment($id, $comment)
    {
        DB::table('comment_to_files')->where('id',  $id)->update(['comment' => $comment]);
    }
}
