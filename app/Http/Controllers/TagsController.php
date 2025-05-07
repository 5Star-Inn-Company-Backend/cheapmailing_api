<?php

namespace App\Http\Controllers;

use App\Models\tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagsController extends Controller
{

    //for apis
    public function createtags(Request $request)
    {
        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        // if (Auth::check()) {
        $request->validate([
            'name' => 'required',
        ]);

        $tag = new tags();
        $tag->business_id = $biz_id;
        $tag->name = $request->name;
        $tag->user_id = Auth::user()->id;
        $tag->save();
        if ($tag->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Tag has been created successfully!',

            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unable to create tag',

            ]);
        }
        // }else{
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Unauthorized',
        //     ]);
        // }
    }

    public function edittags($id)
    {
        if (Auth::check()) {

            $edit = tags::find($id);
            return response()->json([
                'status' => true,
                'message' => $edit,

            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unable to create tag',

            ]);
        }
    }

    public function updatetags(Request $request, $id)
    {
        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        if (Auth::check()) {
            $updatetags = tags::find($id);
            if (tags::where('business_id', $biz_id)) {
                $updatetags->name = $request->name;
                $updatetags->update();

                if ($updatetags->update()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'You successfully updated your tag',
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unable to update your tag',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorize to carry out this action',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unable to create tag',

            ]);
        }
    }

    public function viewtags()
    {
        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        if (Auth::check()) {
            $tag = tags::where('business_id', $biz_id)->get();
            return response()->json([
                'status' => true,
                'message' => $tag,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function viewGroups()
    {
        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        if (Auth::check()) {
            $tag = tags::where('business_id', $biz_id)->with("subscribers")->get();
            return response()->json([
                'status' => true,
                'message' => $tag,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

}
