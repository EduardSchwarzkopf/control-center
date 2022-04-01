<?php

namespace App\Http\Controllers;

use App\Models\ClientEnvironment;
use Illuminate\Http\Request;

class EnvironmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ClientEnvironment::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return ClientEnvironment::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientEnvironment  $clientEnvironment
     * @return \Illuminate\Http\Response
     */
    public function show(ClientEnvironment $clientEnvironment)
    {
        return $clientEnvironment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientEnvironment  $clientEnvironment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClientEnvironment $clientEnvironment)
    {
        $clientEnvironment->update($request->all());
        $clientEnvironment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientEnvironment  $clientEnvironment
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientEnvironment $clientEnvironment)
    {
        $clientEnvironment->delete();
        return response()->noContent();
    }
}
