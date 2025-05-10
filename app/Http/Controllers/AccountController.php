<?php

namespace App\Http\Controllers;

use App\Models\business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{

    public function updateprofile(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'profile' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        // Get the authenticated user
        $user = User::where('id', Auth::user()->id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }

        // Delete the old profile picture if it exists
        if ($user->profile && file_exists(public_path('uploads/profile/' . $user->profile))) {
            unlink(public_path('uploads/profile/' . $user->profile));
        }

        // Upload the new profile picture
        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/profile/');
            $image->move($destinationPath, $name);

            // Update the user's profile picture in the database
            $user->profile = asset('uploads/profile/' . $name);
            $user->save();

            // Return a success response
            return response()->json([
                'status' => true,
                'message' => 'Profile picture updated successfully.',
                'data' => [
                    'profile' => $user->profile,
                    'profilepath' => asset('uploads/profile/' . $name)
                ],
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No file uploaded.',
            ]);
        }
    }

    public function viewuserinfo()
    {
        $user=Auth::user();

        if(!$user->api_token) {
            $data = User::where('id', Auth::id())->with('business')->get();
            return response()->json([
                'status' => true,
                'message' => "Fetched successfully",
                'data' => $data
            ]);
        }else{
            return response()->json([
                'status' => true,
                'message' => "Fetched successfully",
                'data' => $user
            ]);
        }
    }

    public function updateuserinfo(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'address1' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all()),
            ]);
        }

        $userinfo = User::where('id', Auth::user()->id)->first();
        if(!$userinfo){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
//        $userinfo->email = $request->email;
//        $userinfo->company = $request->company;
        $userinfo->phone = $request->phone;
//        $userinfo->zip_code = $request->zip_code;
        $userinfo->state = $request->state;
        $userinfo->city = $request->city;
        $userinfo->address1 = $request->address1;
//        $userinfo->address2 = $request->address2;
        $userinfo->country = $request->country;

        if($userinfo->update()){
            return response()->json([
                'status' => true,
                'message' => 'User info updated successfully!',
                'data' => $userinfo
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unable to update user Info!',
            ]);
        }

    }

    public function businessToken(Request $request)
    {
        $biz=business::find(Auth::user()->business_id);

        return response()->json([
            'status' => true,
            'message' => 'Token fetched successfully!',
            'data' => $biz->api_key
        ]);
    }

    public function businessTokenCreate(Request $request)
    {

        $biz=business::find(Auth::user()->business_id);


        $biz->api_key="CH_".md5(uniqid().time());
        $biz->save();

        return response()->json([
            'status' => true,
            'message' => 'Token generated successfully!',
            'data' => $biz->api_key
        ]);
    }


}
