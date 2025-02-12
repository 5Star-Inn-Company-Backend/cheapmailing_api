<?php

namespace App\Http\Controllers;

use App\Mail\PasswordReset;
use App\Models\business;
use App\Models\password_reset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $business = new business();
        $business->name = $request->name;
        $business->save();
        $id = $business->id;

        $user = new User();
        $user->business_id = $id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => "User Registered Successfully!"], 200);

    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json(["status" => false, 'message' => 'Wrong password or email!'], 401);
        }

        $user = $request->user();
        $tokenresult = $user->createToken('Personal Access Token');
        $token = $tokenresult->plainTextToken;
        $expires_at = Carbon::now()->addweeks(1);

        return response()->json(["status" => true, 'data' => [
            'user' => Auth::user(),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expires_at,
        ]]);

    }

    public function forgetpassword(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                $token = Str::random(30);
//                $domain = URL::to('/');
//                $url = $domain . '/resetpass?token=' . $token;
//
//                $data['url'] = $url;
//                $data['email'] = $request->email;
//                $data['title'] = 'Password Reset';
//                $data['body'] = 'Please click on below link to Reset your password!';
//
//                Mail::send('resetpassmail', ['data' => $data], function ($message) use ($data) {
//                    $message->to($data['email'])->subject($data['title']);
//
//                });
//
//                $datetime = Carbon::now()->format('Y-m-d H:i:s');
//
//                password_reset::updateOrCreate(
//                    ['email' => $request->email],
//
//                    [
//                        'email' => $request->email,
//                        'token' => $token,
//                        'created_at' => $datetime,
//
//                    ]
//                );

                $user->password=Hash::make($token);
                $user->save();

                Mail::to($user->email)->send(new PasswordReset($token));

                return response()->json([
                    'status' => true,
                    'message' => 'A temporary password has been sent to your mail',
                ]);
            } else {


                return response()->json([
                    'status' => false,
                    'message' => 'User not Found!',
                ]);
            }

        } catch (\Exception$e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }


    // public function resetpass(Request $request)
    // {
    //     $resetd = password_reset::where('token', $request->token)->get();

    //     if (isset($request->token) && count($resetd) > 0) {

    //         $user = User::where('email', $resetd[0]['email'])->get();
    //         return view('reset-password', compact('user'));

    //     } else {
    //         return 'No';
    //     }
    // }

    // public function updatepass(Request $request)
    // {
    //     $request->validate([
    //         'password' => 'required|string|min:6|confirmed',
    //         'confirm_pass' => 'required|string|min:6|same:password',
    //     ]);

    //     $user = User::find($request->id);
    //     $user->password = Hash::make($request->password);
    //     $user->save();

    //     password_reset::where('email', $user->email)->delete();

    //     return "<h1>Your Password Reset was Successful!</h1>";

    // }

    public function resetpass(Request $request)
    {
        if (isset($request->token)) {
            $resetData = Password_reset::where('token', $request->token)->first();

            if ($resetData) {
                $user = User::where('email', $resetData->email)->first();
                // dd($user['email']);
                return view('reset_password', compact('user'));
            } else {
                return 'Token not found';
            }
        } else {
            return 'No token provided';
        }
    }

    public function updatepass(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6',
            'confirm_pass' => 'required|same:password',
        ]);

        $user = User::where('email',$request->email)->first();
        // dd($request->email);
        if($user)
        {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            password_reset::where('email', $user->email)->delete();
        }
        return "<h1>Your Password Reset was Successful!</h1>";

    }

    public function changepass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6',
            'confirm_pass' => 'required|same:password',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation fails',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = $request->user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Password Updated Successfully',
            ], 200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Old Password does not match',
            ], 400);
        }
    }
}
