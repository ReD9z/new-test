<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;
use App\Models\Files;

use Illuminate\Database\Eloquent\Model;

class Entrances extends Model
{
    public static function saveFile($file) {
        $image = $file; 
        preg_match("/data:image\/(.*?);/",$image,$image_extension); 
        $image = preg_replace('/data:image\/(.*?);base64,/','',$image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'image_' . time() . '.' . $image_extension[1];
        Storage::disk('public')->put('upload/'.$imageName, base64_decode($image));
        
        $path = Storage::url('upload/'.$imageName);
        
        $uploadfile = new Files();
        $uploadfile->url = $path;
        $uploadfile->save();
        return $uploadfile->id;
    }

    public function files() {
        return $this->hasOne('App\Models\Files', 'id', 'file_id');
    }

    public function address()
    {
        return $this->hasOne('App\Models\Address', 'id', 'address_id');
    }

    public function addressToOrders()
    {
        return $this->hasMany(addressToOrders::class);
    }
}
