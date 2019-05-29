<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
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
        if ($request->search) {
           $address = Address::where('city', 'like', '%' . $request->search . '%')
            ->orWhere('area', 'like', '%' . $request->search . '%')
            ->orWhere('street', 'like', '%' . $request->search . '%')
            ->orWhere('house_number', 'like', '%' . $request->search . '%')
            ->orWhere('number_entrances', 'like', '%' . $request->search . '%')
            ->paginate(6); 
            //TODO сделать метод в модели с несколькими параметрами find('title', 'text' ..).paginate(4)
        } else {
           $address = Address::orderBy($request->sortTable, $request->sort)->paginate(6);
        }  
        return AddressResource::collection($address);
    }

    public function allAddress(Request $request)
    {
        $address = Address::get();
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
       $address->city = $request->input('city');
       $address->area = $request->input('area');
       $address->street = $request->input('street');
       $address->house_number = $request->input('house_number');
       $address->number_entrances = $request->input('number_entrances');

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
            $address = new Address;
            $address->city = array_values($request[$key])[0] == null ? '' : array_values($request[$key])[0];
            $address->area = array_values($request[$key])[1] == null ? '' : array_values($request[$key])[1];
            $address->street = array_values($request[$key])[2] == null ? '' : array_values($request[$key])[2];
            $address->house_number = array_values($request[$key])[3] == null ? '' : array_values($request[$key])[3];
            $address->number_entrances = array_values($request[$key])[4] == null ? '' : array_values($request[$key])[4];
            $address->save();
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
