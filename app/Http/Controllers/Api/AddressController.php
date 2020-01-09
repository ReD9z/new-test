<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\AddressImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Address;
use App\Models\Areas;
use App\Models\CitiesToWorks;
use App\Http\Resources\Address as AddressResource;
use App\Http\Resources\AddressRoleUser as AddressRoleUserResource;
use App\Http\Resources\CitiesToWorks as CitiesToWorksResource;
use App\Http\Resources\Areas as AreasResource;


class AddressController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toWorks = CitiesToWorks::get();
        $areas = Areas::with('cities')->where('city_id', $request->areaCity)->get();
        AddressResource::using($request->order);
        AddressRoleUserResource::using($request->order);
        if($request->city) {
            $address = AddressResource::collection(Address::with('cities', 'areas', 'orderAddress', 'orderAddress.files', 'orderAddress.orders', 'entrances')->where('city_id', $request->city)->get());
        }
        else if($request->user) {
            $address = AddressRoleUserResource::collection(Address::with('cities', 'areas', 'orderAddress', 'orderAddress.files', 'orderAddress.orders', 'entrances')->get());
        }
        else {
            $address = AddressResource::collection(Address::with('cities', 'areas', 'orderAddress', 'orderAddress.files', 'orderAddress.orders', 'entrances')->get());
        }
        
        return response()->json(
            [
                'address' => $address, 
                'city' => CitiesToWorksResource::collection($toWorks), 
                'area' => AreasResource::collection($areas)
            ]
        );
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
        $address->coordinates = Address::getCoordinates($request->input('city').", " . $request->input('street') .", ". $request->input('house_number'));
        
        if($address->save()) {
            return new AddressResource($address);
        }
    }

    public function addExcelData(Request $request)
    {
        Excel::import(new AddressImport, $request->file('file'));
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
