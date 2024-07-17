<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\WebRTCSignal;

class WebRTCController extends Controller
{
    public function sendSignal(Request $request)
    {
        broadcast(new WebRTCSignal($request->type, $request->data, $request->userId));
        return response()->json(['status' => 'Signal sent!']);
    }
}

