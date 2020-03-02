<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Tasks;
use App\Models\Address;
use App\Models\Orders;
use App\Models\Entrances;
use App\Models\TypesToWorks;
use App\Models\Installers;
use App\Models\AddressToOrders;
use App\Http\Resources\Tasks as TasksResource;
use App\Http\Resources\Orders as OrdersResource;
use App\Http\Resources\TaskAddress as TaskAddressResource;
use App\Http\Resources\TaskM as TaskMResource;
use App\Http\Resources\Entrances as EntrancesResource;
use App\Http\Resources\TypesToWorks as TypesToWorksResource;
use App\Http\Resources\Installers as InstallersResource;
use App\Http\Resources\AddressToOrdersTasks as AddressToOrdersTasksResource;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ordersId = []; 
        $orders = [];

        $types = TypesToWorks::get(); 
        $installers = Installers::with('users', 'cities', 'moderator.users')->get();
        $ordersArray = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->get()->where('tasks','=', null);

        if($request->dateStart || $request->dateEnd) {
            $dateStart = Carbon::createFromFormat('d.m.Y', Carbon::parse($request->dateStart)->format('d.m.Y'))->timestamp;
            $dateEnd = Carbon::createFromFormat('d.m.Y', Carbon::parse($request->dateEnd)->format('d.m.Y'))->timestamp;
            foreach ($ordersArray as $key => $value) {
                $itemDateStart = Carbon::createFromFormat('d.m.Y', Carbon::parse($value->order_start_date)->format('d.m.Y'))->timestamp;
                $itemDateEnd = Carbon::createFromFormat('d.m.Y', Carbon::parse($value->order_start_date)->format('d.m.Y'))->timestamp;
                if($dateStart >= $itemDateStart && $dateEnd <= $itemDateEnd || $dateStart <= $itemDateStart && $dateEnd >= $itemDateEnd) {
                    $ordersId[] = $ordersArray[$key]['id'];
                }
            }
            if(empty($request->cityOrder)) {
                $orders = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->whereIn('id', $ordersId)->get()->where('clients.city_id', $request->cityOrder)->where('tasks','=', null);
            } 
            if(!empty($request->cityOrder)) {
                $orders = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->whereIn('id', $ordersId)->get()->where('tasks','=', null);
            }
        } else {
            if(empty($request->cityOrder)) {
                $orders = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->get()->where('clients.city_id', $request->cityOrder)->where('tasks','=', null);
            } 
            if(!empty($request->cityOrder)) {
                $orders = Orders::with('clients', 'orderAddress', 'clients.users', 'tasks')->get()->where('tasks','=', null);
            }
        }
        $status = [
            ['id' => 1, 'title' => 'В работе'], 
            ['id' => 2, 'title' => 'Завершена']
        ];

        $photoDate = [
            ['id' => 1, 'title' => 'Да'], 
            ['id' => 2, 'title' => 'Нет']
        ];

        if(empty($request->city)) {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get()->where('installers.city_id', $request->city); 
        }
        if(empty($request->user)) {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get()->where('installer_id', $request->user); 
        }
        if(!empty($request->city) || !empty($request->user)) {
            $tasks = Tasks::with('orders.clients',  'orders.orderAddress.address.entrances.files', 'installers.users', 'types')->get(); 
        }
    
        return response()->json([
            'tasks' => TasksResource::collection($tasks), 
            'types' => TypesToWorksResource::collection($types), 
            'orders' => OrdersResource::collection($orders),
            'installers' => InstallersResource::collection($installers),
            'statusName' => $status,
            'photoDate' => $photoDate
        ]);
    }

    public function task(Request $request)
    {
        if($request->city) {
            $tasks = Tasks::with(['orders.orderAddress.address.entrances' => function ($query) {
                $query->where('status', '!=', 3);
            }, 'installers.users', 'types', 'orders.clients', 'orders.orderAddress.address.entrances.files'])->get()->where('installers.city_id', $request->city);
        }
        else if($request->user) {
            $tasks = Tasks::with(['orders.orderAddress.address.entrances' => function ($query) {
                $query->where('status', '!=', 3);
            }, 'installers.users', 'types', 'orders.clients', 'orders.orderAddress.address.entrances.files'])->get()->where('installer_id', $request->user);
        }
        else {
            $tasks = Tasks::with(['orders.orderAddress.address.entrances' => function ($query) {
                $query->where('status', '!=', 3);
            }, 'installers.users', 'types', 'orders.clients', 'orders.orderAddress.address.entrances.files'])->get();
        }
    
        $collection = collect(TaskMResource::collection($tasks));
        $collection = $collection->filter(function($value) {
            if($value['number_addresses'] > 0) {
                return $value;
            };
        });

        return $collection;
    }
    
    public function taskAddress($id)
    {
        $tasks = Tasks::with('orders.orderAddress.address')->where('id', $id)->first();
     
        $toOrders = AddressToOrders::with('address')->where('order_id', '=', $tasks['orders_id'])->get();

        $addressArray = collect(TaskAddressResource::collection($toOrders));

        $getAddress = [];

        if($addressArray) {
            foreach ($addressArray as $key => $value) {
                if($value['status'] != 0) {
                    $getAddress[] = $value['id'];
                }
            }
        }
        
        $activeAddress = AddressToOrders::with('address')->whereIn('id', $getAddress)->get();
        
        return AddressToOrdersTasksResource::collection($activeAddress);
    }

    public function taskEntrances($id)
    {
        $entrances = Entrances::with('address')->where('address_to_orders_id', $id)->get();

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
        $tasks->photo_date = $request->input('photo_date');
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
