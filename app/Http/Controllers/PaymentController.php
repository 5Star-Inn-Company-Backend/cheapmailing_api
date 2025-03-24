<?php

namespace App\Http\Controllers;

use App\Models\PricePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function plans(){
        $data=PricePlan::where("status","active")->get();
        return response()->json([
            'status' => true,
            'message' => 'Fetched successfully',
            'data' => $data
        ]);
    }
}
