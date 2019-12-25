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
        $torders = AddressToOrders::with('address', 'orders', 'files')->get();
            
        return AddressToOrdersResource::collection($torders);
    }

    public function indexOne($id)
    {
        $torders = AddressToOrders::with('address', 'orders', 'files')->where('order_id', $id)->get();
            
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
   
        if($request->isMethod('delete') && $request->images) {
            $torders::removeTableFiles($request->images);
        } else {
            if($torders->save()) {
                if ($request->isMethod('post') && is_array($request->images) || $request->isMethod('put') && is_array($request->images)) {
                    foreach ($request->images as $key => $item) {
                        $torders::saveTableFiles(json_decode($request->images[$key])->id, $torders->id);    
                    }
                }
            }
        }
        return new AddressToOrdersResource($torders);
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
