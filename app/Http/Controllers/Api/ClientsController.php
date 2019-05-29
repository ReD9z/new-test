<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Http\Resources\Clients as ClientsResource;

class ClientsController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        if ($request->search) {
            $clients = Clients::with('cities')->where('legal_name', 'like', '%' . $request->search . '%')
                ->orWhere('actual_title', 'like', '%' . $request->search . '%')
                ->orWhere('legal_address', 'like', '%' . $request->search . '%')
                ->orWhere('actual_address', 'like', '%' . $request->search . '%')
                ->orWhere('bank_name', 'like', '%' . $request->search . '%')
                ->orWhere('bik', 'like', '%' . $request->search . '%')
                ->orWhere('cor_score', 'like', '%' . $request->search . '%')
                ->orWhere('settlement_account', 'like', '%' . $request->search . '%')
                ->orWhere('bank_name', 'like', '%' . $request->search . '%')
            ->paginate(6); 
            //TODO сделать метод в модели с несколькими параметрами find('title', 'text' ..).paginate(4)
        } else {
            $clients = Clients::with('cities')->orderBy($request->sortTable, $request->sort)->paginate(6);
        }  
        return ClientsResource::collection($clients);
    }

    public function allClients(Request $request)
    {
        $clients = Clients::get();
        return ClientsResource::collection($clients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $clients = $request->isMethod('put') ? Clients::findOrFail($request->id) : new Clients;

       $clients->id = $request->input('id');
       $clients->city_id = $request->input('city_id');
       $clients->legal_name = $request->input('legal_name');
       $clients->actual_title = $request->input('actual_title');
       $clients->legal_address = $request->input('legal_address');
       $clients->actual_address = $request->input('actual_address');
       $clients->bik = $request->input('bik');
       $clients->cor_score = $request->input('cor_score');
       $clients->settlement_account = $request->input('settlement_account');
       $clients->bank_name = $request->input('bank_name');

        if($clients->save()) {
            return new ClientsResource($clients);
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
        $clients = Clients::findOrFail($id);
        return new ClientsResource($clients);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $clients = Clients::findOrFail($id);

        if($clients->delete()) {
            return new ClientsResource($clients);
        }
    }
}
