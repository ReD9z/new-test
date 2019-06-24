<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Address;
use App\Models\AddressToOrders;
use App\Http\Resources\Orders as OrdersResource;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = Orders::with('clients')->get(); 
        
        return OrdersResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $orders = new Orders;
        
        $orders->id = $request->input('id');
        $orders->clients_id = $request['client']['clients_id'];
        $orders->order_start_date = date("Y-m-d 00:00:00", strtotime($request['dateStart']));
        $orders->order_end_date = date("Y-m-d 00:00:00", strtotime($request['dateEnd']));
        $orders->save();
        
        foreach ($request['address'] as $key => $value) {
            $torders = new AddressToOrders;
            $torders->id = $request->input('id');
            $torders->order_id = $orders->id;
            $torders->address_id = $value['id'];
            $torders->save();
        }
        
        return new OrdersResource($orders);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orders = Orders::findOrFail($id);
        return new OrdersResource($orders);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orders = Orders::findOrFail($id);

        if($orders->delete()) {
            return new OrdersResource($orders);
        }
    }
}
