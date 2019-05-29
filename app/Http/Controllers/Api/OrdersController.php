<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Orders;
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
        if ($request->search) {
            $orders = Orders::join('clients', 'clients.id', '=', 'orders.id')->where('clients.actual_title', 'like', '%' . $request->search . '%')->paginate(6); 
            //TODO сделать метод в модели с несколькими параметрами find('title', 'text' ..).paginate(4)
        } else {
            $orders = Orders::with('clients')->orderBy($request->sortTable, $request->sort)->paginate(6);
        }
        
        return OrdersResource::collection($orders);
    }

    public function allOrders(Request $request)
    {
        $orders = Orders::get();
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
        $orders = $request->isMethod('put') ? Orders::findOrFail($request->id) : new Orders;

        $orders->id = $request->input('id');
        $orders->client_id = $request->input('client_id');
        $orders->order_start_date = $request->input('order_start_date');
        $orders->order_end_date = $request->input('order_end_date');

        if($orders->save()) {
            return new OrdersResource($orders);
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
