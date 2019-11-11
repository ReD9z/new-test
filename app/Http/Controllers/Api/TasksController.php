<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Http\Resources\Tasks as TasksResource;

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
            $tasks = Tasks::with('orders.clients', 'orders.orderAddress', 'orders.orderAddress.address','orders.orderAddress.address.cities', 'orders.orderAddress.address.entrances', 'orders.orderAddress.address.areas', 'installers.users', 'types', 'installers')->get()->where('installers.city_id', $request->city); 
        }
        else if($request->user) {
            $tasks = Tasks::with('orders.clients', 'orders.orderAddress', 'orders.orderAddress.address','orders.orderAddress.address.cities', 'orders.orderAddress.address.entrances', 'installers.users', 'types', 'installers')->get()->where('installer_id', $request->user); 
        }
        else {
            $tasks = Tasks::with('orders.clients', 'orders.orderAddress', 'orders.orderAddress.address','orders.orderAddress.address.cities', 'orders.orderAddress.address.entrances', 'installers.users', 'types', 'installers')->get(); 
        }
        
        return TasksResource::collection($tasks);
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
            if($request->isMethod('put')) {
                $entrances = Entrances::findOrFail($request->id)
                $entrances->shield = $request->input('shield');
                $entrances->glass = $request->input('glass');
                $entrances->information = $request->input('information');
                $entrances->mood = $request->input('mood');
                $entrances->status = $request->input('status');
                if($request->input('image'))) {
                    $entrances->file_id = $entrances::saveFile($request->input('image'));
                }
                $entrances->save();
            }   
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
