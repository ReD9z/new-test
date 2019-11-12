<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Entrances;
use App\Http\Resources\Entrances as EntrancesResource;

class EntrancesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $entrances = Entrances::with('files')->findOrFail($request->id);
        $entrances->shield = $request->input('shield');
        $entrances->glass = $request->input('glass');
        $entrances->information = $request->input('information');
        $entrances->mood = $request->input('mood');
        $entrances->status = $request->input('status');
        if($request->input('image')) {
            $entrances->file_id = $entrances::saveFile($request->input('image'));
        }

        if($entrances->save()) {
            return new EntrancesResource($entrances);
        }
    }
}
