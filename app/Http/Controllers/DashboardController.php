<?php

namespace App\Http\Controllers;

use App\Models\campaign;
use App\Models\subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function main(){
        $data['subscribers'] = subscriber::where('business_id', Auth::user()->business_id)->where('status', 1)->count();
        $data['unsubscribe'] = subscriber::where('business_id', Auth::user()->business_id)->where('status', 0)->count();
        $data['spam_reported'] = subscriber::where('business_id', Auth::user()->business_id)->where('status', 3)->count();
        $data['blacklisted'] = subscriber::where('business_id', Auth::user()->business_id)->where('status', 4)->count();
        $data['last_campaign'] = campaign::where('business_id', auth::user()->business_id)->latest()->first();
        return response()->json([
            'status' => true,
            'message' => "Fetch successfully",
            'data' => $data,
        ]);
    }

    function subscriberGrowth(){
        $growthData = [];
        $currentDate = Carbon::now()->startOfMonth(); // Start of the current month in Ireland timezone

        for ($i = 0; $i < 6; $i++) {
            $startDate = $currentDate->copy()->startOfMonth();
            $endDate = $currentDate->copy()->endOfMonth();

            $subscriberCount = Subscriber::where('business_id', Auth::user()->business_id)->whereBetween('created_at', [$startDate, $endDate])->count();

            $growthData[] = ['key' => $startDate->format('m/Y'), 'value' => $subscriberCount];

            $currentDate->subMonth();
        }

        return response()->json([
            'status' => true,
            'message' => "Fetch successfully",
            'data' => $growthData,
        ]);
    }
}
