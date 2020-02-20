<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clients extends Model
{
    protected $fillable = [
        'users_id',
        'city_id',
        'legal_name',
        'actual_title'
    ];

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

    public static function saveTableFiles($files, $id, $comment)
    {
        DB::table('client_to_files')->insert(['client_id' => $id, 'files_id' => $files]);
        if ($comment) {
            DB::table('comment_to_files')->insert(['client_id' => $id, 'files_id' => $files, 'comment' => $comment]);
        }
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

    public static function mb_ucfirst($word)
    {
        return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
    }

    public static function createClient($cityId, $userId, $nameOrganization)
    {
        $clientId = null;
        
        $clients = Clients::with('cities', 'users', 'comments')->where('users_id', $userId)->first();
        if(!$clients) {
            $client = Clients::create([
                'users_id' => $userId ? $userId : null,
                'city_id' => $cityId ? $cityId : null,
                'legal_name' => $nameOrganization ? self::mb_ucfirst($nameOrganization) : null,
                'actual_title' => $nameOrganization ? self::mb_ucfirst($nameOrganization) : null
            ]);
            $clientId = $client->id;
        } else {
            $clientId = $clients->id;
        }
        
        return $clientId;
    }
}
