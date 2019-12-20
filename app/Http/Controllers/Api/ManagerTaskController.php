<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\ManagerTask;
use App\Models\CitiesToWorks;
use App\Models\Clients;
use App\Models\Managers;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ManagerTaskExport;
use App\Http\Resources\ManagerTask as ManagerTaskResource;
use App\Http\Resources\Clients as ClientsResource;
use App\Http\Resources\Managers as ManagersResource;

class ManagerTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clients = Clients::with('cities', 'users' , 'comments')->get();

        $managers = Managers::with('users', 'cities', 'moderator.users')->get();

        $status = [
            ['id' => 1, 'title' => 'В работе'], 
            ['id' => 2, 'title' => 'Завершена']
        ];

        if($request->city) {
            $tasks = ManagerTask::with('clients', 'managers.users', 'managers')->get()->where('managers.city_id', $request->city); 
        }
         if($request->user) {
            $tasks = ManagerTask::with('clients', 'managers.users', 'managers')->get()->where('manager_id', $request->user); 
        }
        else {
            $tasks = ManagerTask::with('clients', 'managers.users', 'managers')->get(); 
        }

        $collection = collect($tasks);

        if($request->dateStart || $request->dateEnd) {
            $collection = $collection->filter(function($value) use($request)  {
                $dateStart = Carbon::createFromFormat('d.m.Y', Carbon::parse($request->dateStart)->format('d.m.Y'))->timestamp;
                $dateEnd = Carbon::createFromFormat('d.m.Y', Carbon::parse($request->dateEnd)->format('d.m.Y'))->timestamp;
                $itemDate = Carbon::createFromFormat('d.m.Y', Carbon::parse($value->task_date_completion)->format('d.m.Y'))->timestamp;
              
                if($itemDate >= $dateStart && $itemDate <= $dateEnd) {
                    return true;
                } 
            });
        }
        
        return response()->json([
            'tasks' => ManagerTaskResource::collection($collection), 
            'clients' => ClientsResource::collection($clients),
            'managers' => ManagersResource::collection($managers),
            'statusName' => $status
        ]);
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

    public function addExcelTask(Request $request)
    {
        Excel::import(new ManagerTaskExport, $request->file('file'));
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
