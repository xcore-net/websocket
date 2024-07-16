<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MeetingMessage;

class MeetingController extends Controller
{
    public function sendMessage(Request $request)
    {
        broadcast(new MeetingMessage($request->message));
        return response()->json(['status' => 'Message sent!']);
    }
     public function getSomething()
    {
        return view('meeting');
    }
}

