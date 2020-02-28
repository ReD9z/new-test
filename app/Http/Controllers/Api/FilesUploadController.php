<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Files;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use File;
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
