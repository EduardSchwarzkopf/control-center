<?php

namespace App\Http\Controllers;

use App\Models\Heartbeat;

class HeartbeatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($clientId)
    {
        $list = Heartbeat::where('client_id', "=", $clientId)->get();
        return $list;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Heartbeat  $heartbeat
     * @return \Illuminate\Http\Response
     */
    public function show($clientId, $type)
    {
        return Heartbeat::where([['client_id', "=", $clientId], ['type', '=', $type]])->first();
    }
}
