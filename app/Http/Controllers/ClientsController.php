<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;

use App\Client;
use Illuminate\Http\Request;
use Session;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $clients = Client::where('name', 'LIKE', "%$keyword%")
				->orWhere('nit', 'LIKE', "%$keyword%")
				->orWhere('number', 'LIKE', "%$keyword%")
				->orWhere('address', 'LIKE', "%$keyword%")
				->paginate($perPage);
        } else {
            $clients = Client::paginate($perPage);
        }

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $client = new Client;
        $user = new User;
        return view('clients.create',compact('client','user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
     public function validatefields(Request $request)
    {
        $this->validate($request, [
        'username' => 'required|unique:users'
        ]);
    }
    public function store(Request $request)
    {
        $this->validatefields($request);
        $requestData = $request->all();
        $user = new User;
        $user->username = $requestData['username'];
        $user->password = $requestData['password']; 
        $user->email = $requestData['email']; 
        $user->save();
        $client = new Client;
        $client->name = $requestData['name'];
        $client->nit = $requestData['nit'];
        $client->number = $requestData['number'];
        $client->address = $requestData['address'];
        $client->user_id = $user->id;
        $client->save();

        Session::flash('flash_message', 'Client added!');

        return redirect('clients');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);
        $user = User::findOrFail($client->user_id);

        return view('clients.show', compact('client','user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $user = User::findOrFail($client->user_id);

        return view('clients.edit', compact('client','user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        validatefields($request);
        $requestData = $request->all();
        
        $client = Client::findOrFail($id);
        $user = User::findOrFail($client->user_id);
        $client->update($requestData);
        $user->update($requestData);

        Session::flash('flash_message', 'Client updated!');

        return redirect('clients');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->user->delete();
        $client->delete();

        Session::flash('flash_message', 'Client deleted!');

        return redirect('clients');
    }
}