<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function managers()
    {
        return $this->hasOne('App\Models\Managers', 'users_id', 'id');
    }

    public function moderators()
    {
        return $this->hasOne('App\Models\Moderators', 'users_id', 'id');
    }

    public function installers()
    {
        return $this->hasOne('App\Models\Installers', 'users_id', 'id');
    }

    public function clients()
    {
        return $this->hasOne('App\Models\Clients', 'users_id', 'id');
    }

    public static function mb_ucfirst($word)
    {
        return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
    }

    public static function createUser($nameClient, $phone, $email)
    {
        $userId = null;
        $users = User::where('email', self::mb_ucfirst($email))->first();
        if(!$users) {
            $newUsers = new User;
            $newUsers->name =  $nameClient;
            $newUsers->email = $email;
            $newUsers->phone = $phone;
            $newUsers->role = 'client';
            $token = $newUsers->createToken('Laravel Password Grant Client')->accessToken;
            $newUsers->save();
            $userId = $newUsers->id;
        } else {
            $userId = $users->id;
        }
        return $userId;
    }
}
