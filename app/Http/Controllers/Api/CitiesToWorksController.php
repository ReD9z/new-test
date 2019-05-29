<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CitiesToWorks;
use App\Http\Resources\CitiesToWorks as CitiesToWorksResource;

class CitiesToWorksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $toWorks = CitiesToWorks::where('name', 'like', '%' . $request->search . '%')->paginate(6); 
            //TODO сделать метод в модели с несколькими параметрами find('title', 'text' ..).paginate(4)
        } else {
            $toWorks = CitiesToWorks::orderBy($request->sortTable, $request->sort)->paginate(6);
        }
        
        return CitiesToWorksResource::collection($toWorks);
    }

    public function allCities(Request $request)
    {
       
        $toWorks = CitiesToWorks::get();
        
        return CitiesToWorksResource::collection($toWorks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $toWorks = $request->isMethod('put') ? CitiesToWorks::findOrFail($request->id) : new CitiesToWorks;

        $toWorks->id = $request->input('id');
        $toWorks->name = $request->input('name');
    
        if($toWorks->save()) {
            return new CitiesToWorksResource($toWorks);
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
        $toWorks = CitiesToWorks::findOrFail($id);
        return new CitiesToWorksResource($toWorks);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $toWorks = CitiesToWorks::findOrFail($id);

        if($toWorks->delete()) {
            return new CitiesToWorksResource($toWorks);
        }
    }
}
