<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientRessource;
use App\Models\Client;
use App\Models\ClientOption;
use Illuminate\Database\QueryException;

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
    public function store(StoreClientRequest $request)
    {
        $request->merge([
            'url' => rtrim($request->url, "/"),
        ]);

        $client = Client::create($request->all());


        $options = is_array($request->options) ? $request->options : [];
        try {
            $optionsList = ['client_id' => $client->id] + $options;
            ClientOption::create($optionsList);
        } catch (QueryException $ex) {
            abort(422, 'Error: Could not create client.');
        }

        $client = $client->fresh();
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
        return new ClientRessource($client);
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

        $request->merge([
            'url' => rtrim($request->url, "/"),
        ]);

        $client->update($request->all());

        $optionList = $request->options;

        if ($optionList) {
            $options = ClientOption::where('client_id', "=", $client->id)->first();
            $options->update($optionList);
        }

        $client = $client->fresh();

        return new ClientRessource($client);
    }

    /**
     * Search by name
     *
     * @param  str $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $clientList = Client::where('name', 'like', '%' . $name . '%')->get();
        return ClientRessource::collection($clientList);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return response()->noContent();
    }
}
