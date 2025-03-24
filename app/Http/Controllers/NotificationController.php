<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(){
        $data=Auth::user()->unreadNotifications->markAsRead();
        return response()->json([
            'status' => true,
            'message' => 'Marked Successfully'
        ]);
    }

    public function unreadonly(){
        $data=Auth::user()->unreadNotifications;
        return response()->json([
            'status' => true,
            'message' => 'Fetched successfully',
            'data' => $data
        ]);
    }

    public function allNotification(){
        $data=Auth::user()->notifications;
        return response()->json([
            'status' => true,
            'message' => 'Fetched successfully',
            'data' => $data
        ]);
    }
}
