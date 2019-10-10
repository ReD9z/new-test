<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ManagerTask;
use App\Http\Resources\ManagerTask as ManagerTaskResource;

class ManagerTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->city) {
            $tasks = ManagerTask::with('clients', 'managers.users', 'managers')->get()->where('managers.city_id', $request->city); 
        }
        if($request->user) {
            $tasks = ManagerTask::with('clients', 'managers.users', 'managers')->get()->where('manager_id', $request->user); 
        }
        if(!$request->city || !$request->user) {
            $tasks = ManagerTask::with('clients', 'managers.users', 'managers')->get(); 
        }
        
        return ManagerTaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tasks = $request->isMethod('put') ? ManagerTask::findOrFail($request->id) : new ManagerTask;

        $tasks->id = $request->input('id');
        $tasks->client_id = $request->input('client_id');
        $tasks->manager_id = $request->input('manager_id');
        $tasks->status = $request->input('status');
        if($request->input('task_date_completion')) {
            $tasks->task_date_completion = $request->input('task_date_completion') ? date("Y-m-d 00:00:00", strtotime($request->input('task_date_completion'))) : null;
        } else {
            $tasks->task_date_completion = $tasks->status == '2' ? date("Y-m-d 00:00:00") : null;
        }
        $tasks->comment = $request->input('comment');
        $tasks->result = $request->input('result');

        if($tasks->save()) {
            return new ManagerTaskResource($tasks);
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
        $tasks = ManagerTask::findOrFail($id);
        return new ManagerTaskResource($tasks);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tasks = ManagerTask::findOrFail($id);

        if($tasks->delete()) {
            return new ManagerTaskResource($tasks);
        }
    }
}
