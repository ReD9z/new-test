<?php

namespace App\Models;

use App\Models\ModeratorAddresses;

use Illuminate\Database\Eloquent\Model;

class Moderators extends Model
{
    public function users() {
        return $this->belongsTo('App\User', 'users_id');
    }

    public function cities() {
        return $this->belongsTo('App\Models\CitiesToWorks', 'city_id');
    }

    public function addresses() {
        return $this->hasMany('App\Models\ModeratorAddresses', 'moderator_id');
    }
    
    public function getAddress($id)
    {
        $address = ModeratorAddresses::where('moderator_id', $id)->get();
        $cities = [];
        foreach ($address as $key => $value) {
            $cities[$key]['id'] = $value->city->id;
            $cities[$key]['name'] = $value->city->name;
            $cities[$key]['address'] = $value->id;
            $cities[$key]['moderator'] = $value->moderator_id;
        }
        return $cities ? $cities : null;
    }
}
