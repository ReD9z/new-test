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
        if($request->city) {
            $orders = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->get()->where('clients.city_id', $request->city); 
        }
        else if($request->client) {
            $orders = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->get()->where('clients_id', $request->client); 
        }
        else {
            $orders = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->get(); 
        }
        
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
        $orders = $request->isMethod('put') ? Orders::findOrFail($request['order']['id']) : new Orders;
        
        if(!$request->isMethod('put')) {
            $orders->id = $request->input('id');
        }
        
        $orders->clients_id = $request['order']['clients_id'];
        $orders->order_start_date = date("Y-m-d 00:00:00", strtotime($request['dateStart']));
        $orders->order_end_date = date("Y-m-d 00:00:00", strtotime($request['dateEnd']));
        $orders->save();

       
        foreach ($request['address'] as $key => $value) {
            if($request->isMethod('put')) {
                $toOrders = AddressToOrders::where('id', '=', $request['order']['address']['id'])->first();
                if(!$toOrders) {
                    $torders = new AddressToOrders;
                    $torders->id = $request->input('id');
                    $torders->order_id = $orders->id;
                    $torders->address_id = $value['id'];
                    $torders->save();
                    Address::editEntrances($value, $torders->id);
                }
            } else {
                $torders = new AddressToOrders;
                $torders->id = $request->input('id');
                $torders->order_id = $orders->id;
                $torders->address_id = $value['id'];
                $torders->save();
                Address::editEntrances($value, $torders->id);
            }
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
        $orders = Orders::with('orderAddress')->findOrFail($id);
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
