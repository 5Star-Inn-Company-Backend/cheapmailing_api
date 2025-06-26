<?php

namespace App\Http\Controllers;

use App\Models\Blacklisted;
use App\Models\subscriber;
use App\Models\tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function bulkUpload(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(",",$validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;


        $tag=tags::where([['business_id', $biz_id], ['id',$request->tag_id]])->first();
        if(!$tag){
            return response()->json([
                'success' => false,
                'message' => 'Invalid Group provided or does not belongs to you.'
            ], 500);
        }


        // Process the CSV file
        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));


            if(count($data) > 1001){
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum number of records exceeded. The maximum rows is 1,000. Kindly remove some and re-upload'
                ], 500);
            }

            // Extract headers from first row
            $headers = array_map('strtolower', $data[0]);

            // Define database columns (excluding timestamps)
            $dbColumns = [
                'email', 'fname', 'lname',
                'country', 'state', 'phone', 'dob',
                'subscribe', 'status', 'metadata'
            ];

            $results = [
                'total' => count($data) - 1,
                'imported' => 0,
                'failed' => 0,
                'errors' => []
            ];

            // Process rows (skip header row)
            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];

                Log::info("count row:".count(array_filter($row)));
                // Skip empty rows
                if (count(array_filter($row)) === 0) {
                    continue;
                }

                // Combine headers with row data
                $rowData = array_combine($headers, $row);

                Log::info("rowData:".json_encode($rowData));

                // Separate standard columns from extra data
                $subscriberData = [];
                $metadataFields = [];

                foreach ($rowData as $key => $value) {
                    if (in_array($key, $dbColumns)) {
                        // Handle special fields
                        if ($key === 'metadata') {
                            // If metadata is already JSON in the CSV
                            try {
                                $jsonValue = json_decode($value, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $subscriberData[$key] = $jsonValue;
                                } else {
                                    $subscriberData[$key] = $value ? ['csv_metadata' => $value] : [];
                                }
                            } catch (\Exception $e) {
                                $subscriberData[$key] = $value ? ['csv_metadata' => $value] : [];
                            }
                        } else {
                            $subscriberData[$key] = $value;
                        }
                    } else {
                        // Store non-standard fields in metadata
                        $metadataFields[$key] = $value;
                    }
                }


                Log::info("subscriberData:".json_encode($subscriberData));
                Log::info("metadataFields:".json_encode($metadataFields));


                // Merge any existing metadata with extra fields
                if (!empty($metadataFields)) {
//                    if (isset($subscriberData['metadata']) && is_array($subscriberData['metadata'])) {
//                        $subscriberData['metadata'] = array_merge($subscriberData['metadata'], $metadataFields);
//                    } else {
                        $subscriberData['metadata'] = $metadataFields;
//                    }
                }
//
//                // Set default values for subscribe and status if not provided
//                if (!isset($subscriberData['subscribe'])) {
//                    $subscriberData['subscribe'] = 1;
//                }
//
//                if (!isset($subscriberData['status'])) {
//                    $subscriberData['status'] = 1;
//                }

                // Ensure metadata is JSON
//                if (isset($subscriberData['metadata']) && !is_string($subscriberData['metadata'])) {
//                    $subscriberData['metadata'] = json_encode($subscriberData['metadata']);
//                }

                try {

                    // Create new subscriber (or update if email exists)
                    if (isset($subscriberData['email']) && !empty($subscriberData['email'])) {
                        subscriber::updateOrCreate(
                            ['email' => $subscriberData['email'],
                                'business_id' => $biz_id,
                                'tag_id' => $request->tag_id
                                ],
                                $subscriberData
                        );
                        $results['imported']++;
                    } else {
                        $results['failed']++;
                        $results['errors'][] = "Row {$i}: Email is required";
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$i}: " . $e->getMessage();
                    Log::error("CSV import error: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'CSV processing completed',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error("CSV import error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addsubscrib(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'fname' => ['required'],
            'lname' => ['required'],
            'country' => ['required'],
            'state' => ['required'],
            'phone' => ['required'],
            'dob' => ['required'],
            'tag' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        $duplicate = subscriber::where('email', $request->email)->where('phone', $request->phone)->where('business_id',$biz_id)->exists();

        if ($duplicate) {
            return response()->json([
                'status' => false,
                'message' => 'Email or phone number already exist',
            ]);
        }

        $subscrib = new subscriber();
        $subscrib->business_id = $biz_id;
        $subscrib->email = $request->email;
        $subscrib->fname = $request->fname;
        $subscrib->lname = $request->lname;
        $subscrib->country = $request->country;
        $subscrib->state = $request->state;
        $subscrib->phone = $request->phone;
        $subscrib->dob = $request->dob;
        $subscrib->tag_id = $request->tag;

        if ($subscrib->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Subscriber created successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unable to create Subscriber',
            ]);
        }
    }

    public function viewsubscribers()
    {
        if(isset(Auth::user()->api_token)){
            $subscrib = subscriber::where('business_id', Auth::user()->id)->where('status', 1)->with('groups')->latest()->get();

        }else{
            $subscrib = subscriber::where('business_id', Auth::user()->business_id)->where('status', 1)->with('groups')->latest()->paginate(30);
        }

        return response()->json([
            'status' => true,
            'message' => $subscrib,
        ]);
    }


    public function deleteSubscriber($id){

        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        $subscrib = subscriber::where([['id',$id],['business_id', $biz_id]])->first();
        if(!$subscrib){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
        if($subscrib->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Subscriber deleted successfully!',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'unable to delete Subscriber!',
            ]);
        }

    }


    public function blasklisted(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        $subscrib = subscriber::where([['email',$request->email],['business_id', $biz_id]])->first();
        if(!$subscrib){
            return response()->json([
                'status' => false,
                'message' => 'Email not found!',
            ]);
        }

        $subscrib->status = 2;

        if ($subscrib->save()) {
            return response()->json([
                'status' => true,
                'message' => 'You have Successfully Blacklisted this email!',
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Unable to Blacklist this email!',
            ]);
        }
    }

    public function viewblacklisted()
    {

        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        $viewblacklist = subscriber::where([['business_id', $biz_id], ['status',2]])->with('groups')->latest()->get();
        return response()->json([
            'status' => true,
            'message' => $viewblacklist,
        ]);
    }


    public function viewunsubscribers()
    {

        $user=Auth::user();
        $biz_id=isset($user->api_token) ? $user->id : $user-> business_id;

        $subscrib = subscriber::where('business_id', $biz_id)->where('status', 0)->with('groups')->latest()->get();
        return response()->json([
            'status' => true,
            'message' => $subscrib,
        ]);
    }


}
