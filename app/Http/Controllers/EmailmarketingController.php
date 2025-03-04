<?php

namespace App\Http\Controllers;

use App\Models\tags;
use App\Models\User;
use App\Models\country;
use App\Mail\sendemails;
use App\Models\campaign;
use App\Models\template;
use App\Models\Spamreport;
use App\Models\subscriber;
use App\Models\audithtrail;
use App\Models\Blacklisted;
use App\Models\inviteduser;
use Illuminate\Http\Request;
use App\Jobs\CommunicationJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailmarketingController extends Controller
{
    public function createtag(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $tag = new tags();
        $tag->business_id = Auth::user()->business_id;
        $tag->name = $request->name;
        $tag->created_by = Auth::user()->id;
        $tag->save();
        if ($tag->save()) {
            return redirect('viewtag')->with('success', 'Tags created successfully');
        }
    }

    //for viewing tags
    public function viewtag()
    {
        $tag = tags::where('business_id', auth::user()->business_id)->with("user")->get();
        return view('emailmarketing.viewtags', compact('tag'));
    }

    //for subscriber
    public function addsubsc()
    {
        $tag = tags::where('business_id', Auth::user()->business_id)->get();
        $country = country::all();

        return view('emailmarketing.addsubscriber', compact('tag', 'country'));
    }

    public function createsubscriber(Request $request)
    {

        $request->validate([
            'email' => 'required|email|unique:subscribers',
            'fname' => ['required'],
            'lname' => ['required'],
            'country' => ['required'],
            'state' => ['required'],
            'phone' => 'required|min:11',
            'dob' => ['required'],
            'tag' => ['required'],
        ]);

        $subscrib = new subscriber();
        $subscrib->business_id = auth::user()->business_id;
        $subscrib->email = $request->email;
        $subscrib->fname = $request->fname;
        $subscrib->lname = $request->lname;
        $subscrib->country = $request->country;
        $subscrib->state = $request->state;
        $subscrib->phone = $request->phone;
        $subscrib->dob = $request->dob;
        $subscrib->tag_id = $request->tag;
        $subscrib->save();
        if ($subscrib->save()) {
            return redirect()->back()->with('status', "Subscriber successfully addedd");
        }
    }

    public function viewsubscriber()
    {
        $subscrib = subscriber::where('business_id', Auth::user()->business_id)->get();
        return view('emailmarketing.viewsubscriber', compact('subscrib'));
    }


    //for campaign

    public function createcampaign(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'recipient' => 'required',
            'from' => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        $camp = new campaign();
        $camp->business_id = auth::user()->business_id;
        $camp->title = $request->title;
        $camp->recipient = $request->recipient;
        $camp->from = $request->from;
        $camp->subject = $request->subject;
        $camp->content = $request->content;
        $camp->save();
        if ($camp->save()) {
            return redirect()->back()->with('status', "Campaign successfully created");
        }
    }

    public function viewcamp()
    {
        $camp = campaign::where('business_id', auth::user()->business_id)->get();
        return view('emailmarketing.viewcampaign', compact('camp'));
    }

    public function inviteuser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->business_id = Auth::user()->business_id;
        $user->password = Hash::make($request->email);
        $user->save();

        $invited = new inviteduser();
        $invited->user_id = Auth::user()->id;
        $invited->name = $request->name;
        $invited->email = $request->email;
        // $invited->save();

        if ($invited->save()) {

            $mailData = [
                'content' => 'You have been Invited to collaborate with a friend in our workspace by' . ' ' . Auth::user()->name . ' '
                    . 'login to your account using the following details',
                'email' => $request->email,
                'password' => $request->email,
                'dashboard-link' => 'http://localhost:8000/home/' . Auth::user()->id,
            ];

            Mail::to($request->email)->send(new sendemails($mailData));

            return redirect()->back()->with('success', 'You have Successfully Invited' . ' ' . $request->email . ' ' . 'To your account and we have sent them an email notifying Them of your invitation!!');
        }
    }

    public function collaboration()
    {
        $invite = inviteduser::where('email', Auth::user()->email)->first();
        if ($invite) {
            $user = User::where('id', $invite->user_id)->get();
            return view('emailmarketing.collaborations', compact('invite', 'user'));
        } else {
            return redirect()->back()->with('success', 'No Account have invited you to Collaborate with them!!');
        }
    }
    public function collaborators()
    {
        $usersinvited = inviteduser::where('user_id', Auth::user()->id)->get();
        if ($usersinvited) {

            return view('emailmarketing.collaborators', compact('usersinvited'));
        } else {
            return redirect()->back()->with('success', 'You have not invited any Account to collaborate with you');
        }
    }

    //  public function visitaact($id)
    // {

    //     $user = User::find($id)->first();
    //     //   dd($user->id);
    //     return view('dashboard', compact('user'));

    // }

    // public function visitaccount(Request $request)
    // {
    //     Session::flush();

    //     Auth::logout();
    //     //  dd($request->password);

    //     if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
    //         return redirect('/home');
    //     }else{
    //         return redirect()->back()->with('success', 'you cant login');
    //     }
    // }

    //for apis
    public function createtags(Request $request)
    {
        // if (Auth::check()) {
        $request->validate([
            'name' => 'required',
        ]);

        $tag = new tags();
        $tag->business_id = Auth::user()->business_id;
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
        if (Auth::check()) {
            $updatetags = tags::find($id);
            if (tags::where('business_id', Auth::user()->business_id)) {
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
                    'message' => 'You are not authorizr to carry out this action',
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
        if (Auth::check()) {
            $tag = tags::where('business_id', auth::user()->business_id)->get();
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
        if (Auth::check()) {
            $tag = tags::where('business_id', auth::user()->business_id)->with("subscribers")->get();
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

    public function addsubscrib(Request $request)
    {
        // if(Auth::check()){
        $request->validate([
            'email' => 'required',
            'fname' => ['required'],
            'lname' => ['required'],
            'country' => ['required'],
            'state' => ['required'],
            'phone' => ['required'],
            'dob' => ['required'],
            'tag' => ['required'],
        ]);
        $duplicate = subscriber::where('email', $request->email)->where('phone', $request->phone)->exists();

        $subscrib = new subscriber();
        $subscrib->business_id = Auth::user()->business_id;
        $subscrib->email = $request->email;
        $subscrib->fname = $request->fname;
        $subscrib->lname = $request->lname;
        $subscrib->country = $request->country;
        $subscrib->state = $request->state;
        $subscrib->phone = $request->phone;
        $subscrib->dob = $request->dob;
        $subscrib->tag_id = $request->tag;
        if ($duplicate) {
            return response()->json([
                'status' => false,
                'message' => 'Email or phone number already exist',
            ]);
        } else {
            $subscrib->save();
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

        // }else{
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Unauthorized',
        //     ]);
        // }
    }

    public function viewsubscribers()
    {
        if (Auth::check()) {
            $subscrib = subscriber::where('business_id', Auth::user()->business_id)->where('status', 1)->get();
            if ($subscrib) {
                return response()->json([
                    'status' => true,
                    'message' => $subscrib,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No body has subscribe from your website',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function totalsubscriber()
    {
        if (Auth::check()) {
            $totalsubscrib = subscriber::where('business_id', Auth::user()->business_id)->where('status', 1)->count();
            return response()->json([
                'status' => true,
                'message' => $totalsubscrib,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function viewunsubscribers()
    {
        if (Auth::check()) {
            $subscrib = subscriber::where('business_id', Auth::user()->business_id)->where('status', 0)->get();
            if ($subscrib) {
                return response()->json([
                    'status' => true,
                    'message' => $subscrib,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No body has unsubscribe from your website',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function createcampaigns(Request $request)
    {
        // $camp = Campaign::findOrFail(37);
        // $data['campaign'] = $camp;
        // $data['subscribers'] = Subscriber::where('tag_id', $camp->tag_id )->get();
        // CommunicationJob::dispatch($data);

        // dd($camp);

        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|array',
            'content' => 'required',
            'content_type' => 'required',
            'schedule_date' => 'required',
            'status' => 'required',
            'title' => 'required',
            'reply_to' => 'required',
            'from_name' => 'required',
            'from_email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => implode(",", $validator->errors()->all()), 'error' => $validator->errors()]);
        }

        $subscribers=[];

        foreach ($request->tag_id as $tagie){

            $subscribert=Subscriber::where([['tag_id', $tagie], ['status',1], ['business_id',Auth::user()->business_id]])->get()->toArray();

            if(count($subscribert) < 1){
                return response()->json([
                    'status' => false,
                    'message' => 'One of the subscriber list on the recipient group is empty. Remove it and try again',
                ]);
            }

            $subscribers=array_merge($subscribers,$subscribert);

        }

        $camp = new campaign();
        $camp->business_id = Auth::user()->business_id;
        $camp->tag_id = implode(",",$request->tag_id);
        $camp->title = $request->title;
        $camp->reply_to = $request->reply_to;
        $camp->from_name = $request->from_name;
        $camp->from_email = $request->from_email;
        $camp->subject = $request->subject;
        $camp->content = $request->content;
        $camp->content_type = $request->content_type;
        $camp->schedule_date = $request->schedule_date;
        $camp->status = $request->status;

        $camp->save();
        if ($camp->save()) {

            //Do not remove
//            $data['campaign'] = $camp->toArray();
//            $data['subscribers'] = $subscribers;
//            CommunicationJob::dispatch($data);

            //do not remove ends

            return response()->json([
                'status' => true,
                'message' => 'Campaign created Successfully!',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unable to create campaign',
            ]);
        }
    }

    public function viewcamps()
    {
        if (Auth::check()) {
            $camp = campaign::where('business_id', auth::user()->business_id)->get();
            return response()->json([
                'status' => true,
                'message' => $camp,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function inviteusers(Request $request)
    {
        if (Auth::check()) {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
            ]);

            $pass=rand();

            $fu=User::where('email',$request->email)->first();

            if($fu){
                return response()->json([
                    'status' => false,
                    'message' => "An account already exist for this user",
                ]);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->business_id = Auth::user()->business_id;
            $user->password = $pass;
            $user->save();

            $invited = new inviteduser();
            $invited->user_id = Auth::user()->id;
            $invited->name = $request->name;
            $invited->email = $request->email;
            // $invited->save();

            if ($invited->save()) {

                $mailData = [
                    'content' => 'You have been Invited to collaborate with a friend in our workspace by' . ' ' . Auth::user()->name . ' '
                        . 'login to your account using the following details',
                    'email' => $request->email,
                    'password' => $pass,
                    'dashboard-link' => env('FONTEND_URL','https://cheapmailing.dev.5starcompany.com.ng/dashboard'),
                ];

                Mail::to($request->email)->send(new sendemails($mailData));
                return response()->json([
                    'status' => true,
                    'message' => 'You have Successfully Invited' . ' ' . $request->email . ' to your account and we have sent an email notifying them of your invitation!!',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }
    public function collaborations()
    {
        if (Auth::check()) {
            $invite = inviteduser::where('email', Auth::user()->email)->first();
            if ($invite) {
                $user = User::where('id', $invite->user_id)->get();
                return response()->json([
                    'status' => true,
                    'message' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'success' => 'No Account have invited you to Collaborate with them!!',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function collaborator()
    {
        if (Auth::check()) {
            $usersinvited = inviteduser::where('user_id', Auth::user()->id)->get();
            if ($usersinvited) {

                return response()->json([
                    'status' => true,
                    'message' => $usersinvited,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'You have not invited any Account to collaborate with you',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }
    //for spamreports

    public function create_spamreport(Request $request)
    {
        if (Auth::check()) {
            $request->validate([
                'email' => 'required',
            ]);

            $spam = new Spamreport();
            $spam->business_id = Auth::user()->business_id;
            $spam->email = $request->email;
            $spam->save();
            if ($spam->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'You have Successully Reported this email!',
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Unable to report this email for spam!',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function list_spamreport()
    {
        if (Auth::check()) {
            $spamrep = Spamreport::where('business_id', auth::user()->business_id)->latest()->get();
            if ($spamrep) {
                return response()->json([
                    'status' => true,
                    'message' => $spamrep,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'You have not reported the email for spam',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function blasklisted(Request $request)
    {
        if (Auth::check()) {
            $request->validate([
                'email' => 'required',
            ]);

            $blacklisted = new Blacklisted();

            $blacklisted->business_id = Auth::user()->business_id;
            $blacklisted->email = $request->email;

            $blacklisted->save();
            if ($blacklisted->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'You have Successully Blacklisted this email!',
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Unable to Blacklist this email!',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function viewblacklisted()
    {
        if (Auth::check()) {
            $viewblacklist = Blacklisted::where('business_id', Auth::user()->business_id)->latest()->get();
            if ($viewblacklist) {
                return response()->json([
                    'status' => true,
                    'message' => $viewblacklist,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'You have not blacklisted any email',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    //for activities log
    public function addactivitylog(Request $request)
    {
        if (Auth::check()) {

            $activitylog = new audithtrail();
            $activitylog->user_id = Auth::user()->id;
            $activitylog->business_id = Auth::user()->business_id;
            $activitylog->ip_address = $request->ip();
            // dd($activitylog->ip_address);
            $activitylog->device = $request->header('User-Agent');
            $activitylog->action = $request->action;
            $activitylog->save();
            if ($activitylog->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Activity log successfully saved',
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Unable to save Activity log',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function viewactivitylog()
    {
        if (Auth::check()) {
            $viewactivitylog = audithtrail::where('business_id', Auth::user()->business_id)->latest()->get();
            if ($viewactivitylog) {
                return response()->json([
                    'status' => true,
                    'message' => $viewactivitylog,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'You dont have any activity log yet',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function generaltemp()
    {
        if (Auth::check()) {
            $viewagenetem = template::where('template_type', 'general')->latest()->get();
            if ($viewagenetem) {
                return response()->json([
                    'status' => true,
                    'message' => $viewagenetem,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No template have been uploaded yet yet',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function usertemplate(Request $request)
    {
        if (Auth::check()) {

            $usertemplate = new template();
            $usertemplate->business_id = Auth::user()->business_id;
            $usertemplate->template_name = $request->template_name;
            $usertemplate->template_describ = $request->template_describ;
            $usertemplate->design_content = $request->design_content;
            $usertemplate->design_html = $request->design_html;
            $usertemplate->template_type = $request->template_type;
            $usertemplate->save();
            if ($usertemplate->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Your template has been saved!',
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Unable to save Template',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function viewusertemp()
    {
        if (Auth::check()) {
            $viewausertem = template::where('business_id', Auth::user()->business_id)->latest()->get();
            if ($viewausertem) {
                return response()->json([
                    'status' => true,
                    'message' => $viewausertem,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'You dont have any template yet',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function subscribermail()
    {
        if (Auth::check()) {
            $subscrib = subscriber::where('business_id', Auth::user()->business_id)->where('status', 1)->get();
            if ($subscrib) {
                foreach ($subscrib as $sub) {
                    return response()->json([
                        'status' => true,
                        'message' => $sub->email,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Your email was not found in the subscriber list',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function recentcamp()
    {
        if (Auth::check()) {
            $camp = campaign::where('business_id', auth::user()->business_id)->latest()->take(1)->get();
            return response()->json([
                'status' => true,
                'message' => $camp,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function validateArrayData($data)
    {
        foreach ($data as $key => $values) {
            foreach ($values as $value) {
                if (is_null($value) || $value == '') {
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }
    public function bulksubscribe(Request $request)
    {
        $request->validate([
            'csvfile' => 'required',
            'tag_id' => 'required'
        ]);

        if ($request->hasfile('csvfile')) {
            // return response()->json([
            //     "message" => "Subscriber uploaded successfully"
            // ]);
            $csv = file_get_contents($request->csvfile);


            $array = array_map('str_getcsv', explode(PHP_EOL, $csv));

            $validate = $this->validateArrayData($array);

            // Save the data to the database
            foreach (array_slice($validate, 1) as $values) {
                $data = new subscriber();
                $data->business_id = Auth::user()->business_id;
                $data->tag_id = $request->input('tag_id');
                $data->email = $values[0];
                $data->fname = $values[1];
                $data->lname = $values[2];
                $data->country = $values[3];
                $data->state = $values[4];
                $data->phone = $values[5];
                $data->dob = $values[6];

                // Validate email format
                if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
                    return response()->json([
                        'statusCode' => 422,
                        'message' => 'Invalid email address: ' . $data->email,
                    ]);
                }

                $data->save();
            }

            return response()->json([
                'statusCode' => 200,
                'message' => 'Subscriber uploaded successfully!!',
            ]);
        } else {
            return response()->json([
                'statusCode' => 422,
                'message' => 'No CSV file found',
            ]);
        }
    }

    //for tags under subscribe
    public function tagsubscribe()
    {
        if (Auth::check()) {
            $subscrib = subscriber::where('subscribe', 1)->get();
            $tags = tags::whereIn('id', $subscrib->pluck('tag_id'))->get();
            if($subscrib->count() > 0){
                return response()->json([
                    'status' => true,
                    'message' => $tags,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No data found',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    //for tags under unsubscribe
    public function tagunsubscrib()
    {
        if (Auth::check()) {
            $subscrib = subscriber::where('subscribe', 0)->get();
            $tagsunsubscrib = tags::whereIn('id', $subscrib->pluck('tag_id'))->get();
            if($subscrib->count() > 0){
                return response()->json([
                    'status' => true,
                    'message' => $tagsunsubscrib,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No data found',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

    //for tags under spamreports
    public function tagspam()
    {
        if (Auth::check()) {
            $user = new User();
            $tags = tags::all();
            foreach($tags as $tag) {
                // $user = $tag->user;
                $tagspam = Spamreport::where('email', $tag->user->email)->get();
                if($tagspam->count() > 0){
                    return response()->json([
                        'status' => true,
                        'message' => $tagspam,
                    ]);
                }else{
                    return response()->json([
                        'status' => true,
                        'message' => 'No record found',
                    ]);
                }

            }


        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
    }

     //for tags under blacklist
     public function tagblacklist()
     {
        if (Auth::check()) {
            $user = new User();
            $tags = tags::all();
            foreach($tags as $tag) {
                // $user = $tag->user;
                $tagblacklist = Blacklisted::where('email', $tag->user->email)->get();
                if($tagblacklist->count() > 0){
                    return response()->json([
                        'status' => true,
                        'message' => $tagblacklist,
                    ]);
                }else{
                    return response()->json([
                        'status' => true,
                        'message' => 'No record found',
                    ]);
                }

            }


        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
     }

     public function deletesubscribe($id){
        $subscrib = subscriber::find($id);
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

     public function deletetags($id)
     {
        $tag = tags::where('id',$id)->where('business_id', Auth::user()->business_id);
        if(!$tag){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
        if($tag->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Tag deleted successfully!',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete tag!',
            ]);
        }
     }

     public function deleteblacklist($id)
     {
        $blacklist = Blacklisted::where('id',$id)->where('business_id', Auth::user()->business_id);

        if(!$blacklist){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
        if($blacklist->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Blacklist deleted successfully!',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete blacklist!',
            ]);
        }
     }

     public function deletespam($id){
        $spam = Spamreport::where('id',$id)->where('business_id', Auth::user()->business_id);
        if(!$spam){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
        if($spam->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Spam report deleted successfully!',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete spam report!',
            ]);
        }
     }

     public function deleteunsubscribe($id)
     {
        $unsubscribe = subscriber::where('id', $id)->where('subscribe', 0)->first();
        if(!$unsubscribe){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
        if($unsubscribe->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Unsubscriber deleted successfully!',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete unsubscriber!',
            ]);
        }
     }

     public function deletecamp($id)
     {
        $campaign = campaign::where('id', $id)->where('business_id', Auth::user()->business_id)->first();
        if(!$campaign){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
        if($campaign->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Campaign deleted successfully!',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete Campaign!',
            ]);
        }
     }

     public function deletetempl($id)
     {
        $template = template::where('id', $id)->where('business_id', Auth::user()->business_id)->where('template_type', 'private')->first();
        if(!$template){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
        if($template->delete()){
            return response()->json([
                'status' => true,
                'message' => 'Template deleted successfully!',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete Template!',
            ]);
        }
     }

     public function updateuserinfo(Request $request, $id)
     {
         $userinfo = User::where('id',$id)->where('id', Auth::user()->id)->first();
         if(!$userinfo){
            return response()->json([
                'status' => false,
                'message' => 'Id not found!',
            ]);
        }
        $userinfo->email = $request->email;
        $userinfo->company = $request->company;
        $userinfo->phone = $request->phone;
        $userinfo->zip_code = $request->zip_code;
        $userinfo->state = $request->state;
        $userinfo->city = $request->city;
        $userinfo->address1 = $request->address1;
        $userinfo->address2 = $request->address2;
        $userinfo->country = $request->country;

        if($userinfo->update()){
            return response()->json([
                'status' => true,
                'message' => 'User info updated successfully!',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unable to update user Info!',
            ]);
        }

     }

    public function updateprofile(Request $request, $id)
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
        $user = User::where('id', $id)->where('id', Auth::user()->id)->first();
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
            $user->profile = $name;
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

     public function viewuserinfo($id)
     {
        if (Auth::check()) {
            $viewauser = User::where('id', $id)->where('business_id', Auth::user()->business_id)->get();
            if (!$viewauser) {
                return response()->json([
                    'status' => true,
                    'message' => 'User details not found',
                ]);
            }
            if($viewauser){
                return response()->json([
                    'status' => true,
                    'message' => $viewauser,
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
     }

     public function viewprofile($id)
     {
        if (Auth::check()) {
            $viewaprofile = User::where('id', $id)->where('business_id', Auth::user()->business_id)->first();

            if(!$viewaprofile){
                return response()->json([
                    'status' => false,
                    'message' => 'ID not found',
                ]);
            }
            if($viewaprofile->profile == ""){
                return response()->json([
                    'status' => false,
                    'message' => 'You dont have a profile yet',
                ]);
            }
            $profilePath = asset('uploads/profile/' . $viewaprofile->profile);

            if ($viewaprofile) {
                return response()->json([
                    'status' => true,
                    'message' => $viewaprofile->profile,
                    "profilepath" => $profilePath,

                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'ID not found',
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
        }
     }
}
