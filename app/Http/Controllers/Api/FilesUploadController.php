<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Files;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use File;
use App\Models\AddressToOrders;
use App\Models\Entrances;
use App\Models\ImagesToOrders;
use ZipArchive;

class FilesUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $loadfile = [];   
        foreach ($request->file('file') as $key => $file) {
            $filename = uniqid() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            Storage::disk('public')->putFileAs('upload',  $file, $filename);
            
            $uploadfile = new Files();
            $uploadfile->url = Storage::url('upload/'.$filename);
            $uploadfile->save();
            $loadfile[] = $uploadfile;
        }
        
        return response()->json(['errors' => [], 'files' => $loadfile, 'status' => 200], 200);
    }

    public function downloadAllFiles(Request $request) {
        $torders = AddressToOrders::with('address', 'orders', 'files', 'entrances')->where('order_id', $request->input('id'))->pluck('id')->all();
        $entrances = Entrances::where([
            ['file_id', '!=', null],
            ['status', '=', 3]
        ])->whereIn('address_to_orders_id', $torders)->pluck('file_id')->all();

        $address = ImagesToOrders::with('orders')->whereIn('address_to_orders_id', $torders)->where([
            ['files_id', '!=', null],
        ])->pluck('files_id')->all();
        
        $result = array_merge($entrances, $address);
        
        $files = Files::with('entrances.orderAddress')->whereIn('id', $result)->get();
        
        if($files) {
            $zip = new ZipArchive;
            $names = 'Заказ' . $request->input('id');
            $fileName = "files/{$names}.zip";

            File::delete($fileName);
            if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
            {
                foreach ($files as $key => $value) {
                    $zip->addFile(public_path($value['url'], str_replace("/storage/upload/", "", $value['url'])), str_replace("/storage/upload/", "", $value['url']));
                }
                
                $zip->close();
            }

            return response()->download(public_path($fileName), $names);
        }
    }

    public function downloadFiles(Request $request)
    {
        $zip = new ZipArchive;
        $names = str_replace("/", "-", $request->all()['addressName']);
        $fileName = "files/{$names}.zip";
        File::delete($fileName);
   
        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            foreach ($request->all()['images'] as $key => $value) {
                $zip->addFile(public_path($value['url'], str_replace("/storage/upload/", "", $value['url'])), str_replace("/storage/upload/", "", $value['url']));
            }
            
            $zip->close();
        }

        return response()->download(public_path($fileName), $names);
    }


    public function remove(Request $request)
    {
        $path = Storage::delete('public/'.$request->url);
        $files = Files::findOrFail($request->id);
        $files->delete();

        return response()->json(['errors' => [], 'data' => $files, 'status' => 200], 200);
    }

}
