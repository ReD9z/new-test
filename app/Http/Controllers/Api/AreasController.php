<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Areas;
use App\Http\Resources\Areas as AreasResource;

class AreasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(json_decode($request->city)) {
            $arr = [];
            foreach (json_decode($request->city) as $key => $value) {
                $arr[] = $value->city_id;
            }
            $areas = Areas::with('cities')->whereIn('city_id', $arr)->get();
        }

        if(!json_decode($request->city)) {
            $areas = Areas::with('cities')->get();
        }

        return AreasResource::collection($areas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        function mb_ucfirst($word)
        {
            return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
        }
        
        $areas = $request->isMethod('put') ? Areas::findOrFail($request->id) : new Areas;

        $areas->id = $request->input('id');
        $areas->name = mb_ucfirst($request->input('name'));
        $areas->city_id = mb_ucfirst($request->input('city_id'));

        if($areas->save()) {
            return new AreasResource($areas);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $areas = Areas::findOrFail($id);
        return new AreasResource($areas);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $areas = Areas::findOrFail($id);

        if($areas->delete()) {
            return new AreasResource($areas);
        }
    }
}
