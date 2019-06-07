<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AddressToOrders;
use App\Http\Resources\AddressToOrders as AddressToOrdersResource;

class AddressToOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $torders = AddressToOrders::with('address', 'orders.clients')->get();
            
        return AddressToOrdersResource::collection($torders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $torders = $request->isMethod('put') ? AddressToOrders::findOrFail($request->id) : new AddressToOrders;

        $torders->id = $request->input('id');
        $torders->order_id = $request->input('order_id');
        $torders->address_id = $request->input('address_id');
        $torders->status = $request->input('status');
    
        if($torders->save()) {
            return new AddressToOrdersResource($torders);
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
        $torders = AddressToOrders::findOrFail($id);
        return new AddressToOrdersResource($torders);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $torders = AddressToOrders::findOrFail($id);

        if($torders->delete()) {
            return new AddressToOrdersResource($torders);
        }
    }
}
