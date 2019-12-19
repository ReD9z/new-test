<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ManagerTask;
use App\Models\CitiesToWorks;
use App\Models\Clients;
use App\Models\Managers;
use App\User;
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
        if(!$request->city || !$request->user) {
            $tasks = ManagerTask::with('clients', 'managers.users', 'managers')->get(); 
        }
        
        return response()->json([
            'tasks' => ManagerTaskResource::collection($tasks), 
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
        function mb_ucfirst($word)
        {
            return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($word, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($word), 'UTF-8');
        }


        if(count($request->input()) == 8) {
            $data = [];

            $users = User::where('name', mb_ucfirst(array_values($request->input())[4]))->first();
            $users_id = $users ? $users->id : null;
            
            $citywork = CitiesToWorks::where('name', mb_ucfirst(array_values($request->input())[2]))->first();
            
            $toClients = null;
            
            if(!empty($users)) {
                $clients = Clients::with('cities', 'users', 'comments')->where('users_id', $users_id)->first();
                if(!empty($clients)) {
                    $toClients = $clients->id;
                }else {
                    $clients = new Clients;
                    if (!empty($users)) { 
                        $clients->users_id = $users->id;
                    } else {
                        $toUsers = new User;
                        $toUsers->name = mb_ucfirst(array_values($request->input())[4]);
                        $toUsers->email = str_replace(' ', '', array_values($request->input())[6]);
                        $toUsers->phone = str_replace(' ', '', array_values($request->input())[5]);
                        $toUsers->login = null;
                        $toUsers->role = 'client';
                        
                        $toUsers->password = null;
                        
                        $token = $toUsers->createToken('Laravel Password Grant Client')->accessToken;
                        $toUsers->save();
                        $clients->users_id = $toUsers->id;
                    }

                    if (!empty($citywork)) {
                        $clients->city_id = $citywork->id;
                    } else {
                        $toWorks = new CitiesToWorks;
                        $toWorks->name = mb_ucfirst(array_values($request->input())[2]);
                        $toWorks->save();
                        $clients->city_id = $toWorks->id;
                    }

                    $clients->legal_name = mb_ucfirst(array_values($request->input())[3]);
                    $clients->actual_title = mb_ucfirst(array_values($request->input())[3]);
                    $clients->legal_address = null;
                    $clients->actual_address = null;
                    $clients->bik = null;
                    $clients->cor_score = null;
                    $clients->settlement_account = null;
                    $clients->bank_name = null;
                    $clients->save();
                    $toClients = $clients->id;
                }
            } else {
                $clients = new Clients;
                if (!empty($users)) { 
                    $clients->users_id = $users->id;
                } else {
                    $toUsers = new User;
                    $toUsers->name = mb_ucfirst(array_values($request->input())[4]);
                    $toUsers->email = str_replace(' ', '', array_values($request->input())[6]);
                    $toUsers->phone = str_replace(' ', '', array_values($request->input())[5]);
                    $toUsers->login = null;
                    $toUsers->role = 'client';
                    
                    $toUsers->password = null;
                    
                    $token =  $toUsers->createToken('Laravel Password Grant Client')->accessToken;
                    $toUsers->save();
                    $clients->users_id = $toUsers->id;
                }

                if (!empty($citywork)) {
                    $clients->city_id = $citywork->id;
                } else {
                    $toWorks = new CitiesToWorks;
                    $toWorks->name = mb_ucfirst(array_values($request->input())[2]);
                    $toWorks->save();
                    $clients->city_id = $toWorks->id;
                }

                $clients->legal_name = mb_ucfirst(array_values($request->input())[3]);
                $clients->actual_title = mb_ucfirst(array_values($request->input())[3]);
                $clients->legal_address = null;
                $clients->actual_address = null;
                $clients->bik = null;
                $clients->cor_score = null;
                $clients->settlement_account = null;
                $clients->bank_name = null;
                $clients->save();
                $toClients = $clients->id;
            }

            $tasks = new ManagerTask;

            $tasks->id = $request->input('id');
            $tasks->client_id = $toClients; 
            $tasks->manager_id = null;
            $tasks->status = '2';
            if(array_values($request->input())[1]) {
                $tasks->task_date_completion = array_values($request->input())[1] ? date("Y-m-d 00:00:00", strtotime(array_values($request->input())[1])) : null;
            } else {
                $tasks->task_date_completion = $tasks->status == '2' ? date("Y-m-d 00:00:00") : null;
            }
            $tasks->comment = array_values($request->input())[7];
            $tasks->result = null;
            $tasks->save();

            $data = new ManagerTaskResource($tasks);

            return response()->json(['errors' => [], 'data' => $data, 'status' => 200], 200);
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
