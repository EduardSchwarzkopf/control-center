<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientRessource;
use App\Models\Client;
use App\Models\ClientOption;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ClientRessource::collection(Client::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client = new Client(['name' => $request->name]);
        $client->save();

        try {
            $optionsList = ['client_id' => $client->id] + $request->options;
            ClientOption::create($optionsList);
        } catch (QueryException $ex) {
            $client->delete();
            abort(422, 'Error while inserting Client');
        }

        return ClientRessource::make($client);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        return new ClientResource($client);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(StoreClientRequest $request, Client $client)
    {

        $client->update($request->all());

        $optionList = $request->options;

        if ($optionList) {
            $options = ClientOption::where('client_id',"=", $client->id)->first();
            $options->update($optionList);
        }

        return new ClientRessource($client);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
        $client->delete();
        return response()->noContent();
    }
}
