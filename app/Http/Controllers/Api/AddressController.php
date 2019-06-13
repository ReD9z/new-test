<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
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
        $address = Address::with('cities')->get();
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
        $address->area = $request->input('area');
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
        $data = [];
        foreach ($request->input() as $key => $value) {
            $citywork = CitiesToWorks::where('name', array_values($request[$key])[0])->first();
            $cityId = 0;
            $address = new Address;
            if (!empty($citywork)) {
                $cityId = $citywork->id;
                // $address->city_id = $citywork->id;
                // $address->area = array_values($request[$key])[1] == null ? '' : array_values($request[$key])[1];
                // // $address->street = array_values($request[$key])[2] == null ? '' : array_values($request[$key])[2];
                // // $address->house_number = array_values($request[$key])[3] == null ? '' : array_values($request[$key])[3];
                // // $address->number_entrances = array_values($request[$key])[4] == null ? '' : array_values($request[$key])[4];
                // // $address->management_company = array_values($request[$key])[5] == null ? '' : array_values($request[$key])[5];
                // $address->save();
                // $data[$key] = new AddressResource($address);
            }
            if (empty($citywork)) {
                $toWorks = new CitiesToWorks;
                $toWorks->name = array_values($request[$key])[0] == null ? '' : array_values($request[$key])[0];
                $toWorks->save();
                $cityId = $toWorks->id;
            }
            $address->city_id = $cityId;
            $address->area = array_values($request[$key])[1] == null ? '' : array_values($request[$key])[1];
            $address->save();
            // if (empty($citywork)) { 
            //     $toWorks = new CitiesToWorks;
            //     $toWorks->name = array_values($request[$key])[0];
            //     $toWorks->save();
            //     $address = new Address;
            //     $toWorks->id = $toWorks->id;
            //     $address->area = array_values($request[$key])[1] == null ? '' : array_values($request[$key])[1];
            //     // $address->street = array_values($request[$key])[2] == null ? '' : array_values($request[$key])[2];
            //     // $address->house_number = array_values($request[$key])[3] == null ? '' : array_values($request[$key])[3];
            //     // $address->number_entrances = array_values($request[$key])[4] == null ? '' : array_values($request[$key])[4];
            //     // $address->management_company = array_values($request[$key])[5] == null ? '' : array_values($request[$key])[5];
            //     $address->save();
            //     $data[$key] = new AddressResource($address);
            // }
            // else {
            //     $toWorks = new CitiesToWorks;
            //     $toWorks->id = $request->input('id');
            //     $toWorks->name = array_values($request[$key])[0];
            //     $toWorks->save();
            //     $address = new Address;
            //     $toWorks->id = $toWorks->id;
            //     $address->area = array_values($request[$key])[1] == null ? '' : array_values($request[$key])[1];
            //     $address->street = array_values($request[$key])[2] == null ? '' : array_values($request[$key])[2];
            //     $address->house_number = array_values($request[$key])[3] == null ? '' : array_values($request[$key])[3];
            //     $address->number_entrances = array_values($request[$key])[4] == null ? '' : array_values($request[$key])[4];
            //     $address->management_company = array_values($request[$key])[5] == null ? '' : array_values($request[$key])[5];
            //     $address->save();
            //     $data[$key] = new AddressResource($address);
            // }
            
            // $address = new Address;
            // $citywork = CitiesToWorks::where('name', 'like', '%'.array_values($request[$key])[0] == null ? '' : array_values($request[$key])[0].'%')->get();
    
            // $address->city_id = CitiesToWorks::Test(array_values($request[$key])[0]);
            // $address->area = array_values($request[$key])[1] == null ? '' : array_values($request[$key])[1];
            // $address->street = array_values($request[$key])[2] == null ? '' : array_values($request[$key])[2];
            // $address->house_number = array_values($request[$key])[3] == null ? '' : array_values($request[$key])[3];
            // $address->number_entrances = array_values($request[$key])[4] == null ? '' : array_values($request[$key])[4];
            // $address->management_company = array_values($request[$key])[5] == null ? '' : array_values($request[$key])[5];
            // $address->save();
            $data[$key] = new AddressResource($address);
        }
        return response()->json(['errors' => [], 'data' => $data, 'status' => 200], 200);
        
     
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
