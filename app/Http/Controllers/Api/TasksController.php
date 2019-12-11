<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Models\Address;
use App\Models\Orders;
use App\Models\Entrances;
use App\Http\Resources\Tasks as TasksResource;
use App\Http\Resources\Orders as OrdersResource;
use App\Http\Resources\TaskAddress as TaskAddressResource;
use App\Http\Resources\TaskM as TaskMResource;
use App\Http\Resources\Entrances as EntrancesResource;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->city) {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get()->where('installers.city_id', $request->city); 
        }
        else if($request->user) {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get()->where('installer_id', $request->user); 
        }
        else {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get(); 
        }

        return TasksResource::collection($tasks);
    }

    public function task(Request $request)
    {
        if($request->city) {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get()->where('installers.city_id', $request->city); 
        }
        else if($request->user) {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get()->where('installer_id', $request->user); 
        }
        else {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get(); 
        }

        return TaskMResource::collection($tasks);
    }
    
    public function taskAddress($id)
    {
        $tasks = Tasks::with('orders.orderAddress.address')->where('id', $id)->first();
    
        $address = [];

        foreach ($tasks->orders->orderAddress as $key => $value) {
            $address[] = $value->address_id;
        }

        $addresses = Address::with('cities', 'areas', 'orderAddress', 'orderAddress.files', 'orderAddress.orders', 'entrances')->whereIn('id', $address)->get();

        return TaskAddressResource::collection($addresses);
    }

    public function taskEntrances($id)
    {
        $entrances = Entrances::with('address')->where('address_id', $id)->get();

        return EntrancesResource::collection($entrances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tasks = $request->isMethod('put') ? Tasks::findOrFail($request->id) : new Tasks;

        $tasks->id = $request->input('id');
        $tasks->orders_id = $request->input('orders_id');
        $tasks->installer_id = $request->input('installer_id');
        $tasks->types_to_works_id = $request->input('types_to_works_id');
        $tasks->status = $request->input('status');
        $tasks->task_date_completion = date("Y-m-d 00:00:00", strtotime($request->input('task_date_completion')));
        $tasks->comment = $request->input('comment');
         
        if($tasks->save()) {
            return new TasksResource($tasks);
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
        $tasks = Tasks::findOrFail($id);
        return new TasksResource($tasks);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tasks = Tasks::findOrFail($id);

        if($tasks->delete()) {
            return new TasksResource($tasks);
        }
    }
}
