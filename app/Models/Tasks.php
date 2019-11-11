<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Address;
use App\Models\Entrances;
use Illuminate\Support\Facades\Storage;
use App\Models\Files;

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

    public function countAdresses($address)
    {
        $count = 0;
        foreach ($address as $key => $value) {
            $count = $count + $value->address->number_entrances;
        }
        return $count;
    }

    public static function saveFile() {
        $image = $request->input('image'); 
        preg_match("/data:image\/(.*?);/",$image,$image_extension); // extract the image extension
        $image = preg_replace('/data:image\/(.*?);base64,/','',$image); // remove the type part
        $image = str_replace(' ', '+', $image);
        $imageName = 'image_' . time() . '.' . $image_extension[1]; //generating unique file name;
        Storage::disk('public')->put('upload/'.$imageName, base64_decode($image));
        
        $path = Storage::url('upload/'.$imageName);
        
        $uploadfile = new Files();
        $uploadfile->url = $path;
        $uploadfile->save();
    }
}
