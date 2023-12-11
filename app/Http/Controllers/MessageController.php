<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function aimessage(Request $request)
    {
        $curl = curl_init();
    
        $message = [
            "message" => $request->message
        ];
    
        $encodemessage = json_encode($message);
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://resources.5starcompany.com.ng/api/aibot',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $encodemessage,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
    
        // Decode the JSON response
        $responseData = json_decode($response, true);
    
        // Return a JSON response in Laravel
        return response()->json(['status' => 'success', 'data' => $responseData]);
    }
    
}
