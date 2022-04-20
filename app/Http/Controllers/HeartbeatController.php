<?php

namespace App\Http\Controllers;

use App\Models\Heartbeat;
use Illuminate\Http\Request;

class HeartbeatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($clientId)
    {
        $list = Heartbeat::where('client_id', "=", $clientId)->latest()->get();
        return $list;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Heartbeat  $heartbeat
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $clientId, $type)
    {
        $amount = $request->amount;

        $maxAmount = 30;
        if (is_numeric($amount) && ($amount < 0 || $amount > $maxAmount)) {
            $amount = $maxAmount;
        }

        return Heartbeat::where([['client_id', "=", $clientId], ['type', '=', $type]])->orderBy('created_at', 'asc')->latest()->take($amount)->get();
    }
}
