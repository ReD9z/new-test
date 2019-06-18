<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Areas;
use App\Models\CitiesToWorks;
use App\Http\Resources\Address as AddressResource;

class AddressController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $address = Address::with('cities', 'areas', 'orderAddress', 'orderAddress.files')->get();
        return AddressResource::collection($address);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $address = $request->isMethod('put') ? Address::findOrFail($request->id) : new Address;

        $address->id = $request->input('id');
        $address->city_id = $request->input('city_id');
        $address->area_id = $request->input('area_id');
        $address->street = $request->input('street');
        $address->house_number = $request->input('house_number');
        $address->number_entrances = $request->input('number_entrances');
        $address->management_company = $request->input('management_company');
    
        if($address->save()) {
            return new AddressResource($address);
        }
    }

    public function addExcelData(Request $request)
    {
        //TODO уведомлять пользователя о незаполненых пустых полях - валидация
        //TODO создать метод в модели и вызвать

        function mb_ucfirst($word)
        {
            return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
        }

        if(count($request->input()) == 6) {
            $data = [];
            $citywork = CitiesToWorks::where('name', mb_ucfirst(array_values($request->input())[0]))->first();
            $area = Areas::where('name', mb_ucfirst(array_values($request->input())[1]))->first();
            $address = new Address;
            if (!empty($citywork)) {
                $address->city_id = $citywork->id;
            }
            if (empty($citywork)) {
                $toWorks = new CitiesToWorks;
                $toWorks->name = mb_ucfirst(array_values($request->input())[0]);
                $toWorks->save();
                $address->city_id = $toWorks->id;
            }       

            if (!empty($area)) {
                $address->area_id = $area->id;
            }

            if (empty($area)) {
                $areas = new Areas;
                $areas->name = mb_ucfirst(array_values($request->input())[1]);
                $areas->save();
                $address->area_id = $areas->id;
            } 

            $address->street = array_values($request->input())[2];
            $address->house_number = array_values($request->input())[3];
            $address->number_entrances = array_values($request->input())[4];
            $address->management_company = array_values($request->input())[5];
            $address->save();
            
            $data = new AddressResource($address);
            return response()->json(['errors' => [], 'data' => $data, 'status' => 200], 200);
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
        $address = Address::findOrFail($id);
        return new AddressResource($address);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $address = Address::findOrFail($id);

        if($address->delete()) {
            return new AddressResource($address);
        }
    }
}
