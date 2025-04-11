<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\EmailmarketingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('dashboard/', function () {
    return view('dashboard');
});


// Route::get('addtag', function () {
//     return view('emailmarketing.addtags');
// });

// Route::get('adduser', function () {
//     return view('emailmarketing.adduser');
// });

// Route::get('addcamp', function () {
//     return view('emailmarketing.createcampaign');
// });

Route::post('forgetpas', [UserController::class, 'forgetpassword']);
Route::post('registering', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('resetpass', [UserController::class, 'resetpass']);
Route::post('updatepassword', [UserController::class, 'updatepass']);
// Route::get('viewtags', [EmailmarketingController::class, 'viewtags'])->name('viewtags');


Route::group(['middleware' => 'auth:sanctum'], function (){
    Route::post('changepassword', [UserController::class, 'changepass']);
    Route::post('createtags', [EmailmarketingController::class, 'createtags'])->name('createtags');
    Route::post('addsubscrib', [EmailmarketingController::class, 'addsubscrib'])->name('addsubscrib');
    // Route::post('createsubscriber', [EmailmarketingController::class, 'createsubscriber'])->name('createsubscriber');
    Route::get('viewtags', [EmailmarketingController::class, 'viewtags'])->name('viewtags');
    Route::get('view-groups', [EmailmarketingController::class, 'viewGroups'])->name('viewgroups');
    Route::get('edittags/{id}', [EmailmarketingController::class, 'edittags'])->name('edittags');
    Route::put('updatetags/{id}', [EmailmarketingController::class, 'updatetags'])->name('updatetags');
    Route::get('viewsubscrib', [EmailmarketingController::class, 'viewsubscribers'])->name('viewsubscrib');
    Route::get('unsubscribe', [EmailmarketingController::class, 'viewunsubscribers'])->name('unsubscribe');
    Route::post('createcampaigns', [emailmarketingController::class, 'createcampaigns'])->name('createcampaigns');
    Route::get('viewcamps', [emailmarketingController::class, 'viewcamps'])->name('viewcamps');
    Route::post('inviteusers', [EmailmarketingController::class, 'inviteusers'])->name('inviteusers');
    Route::get('collaborations', [EmailmarketingController::class, 'collaborations'])->name('collaborations');
    Route::get('collaborators', [EmailmarketingController::class, 'collaborator'])->name('collaborators');
    Route::post('create_spamreport', [EmailmarketingController::class, 'create_spamreport'])->name('create_spamreport');
    Route::get('list_spamreport', [EmailmarketingController::class, 'list_spamreport'])->name('list_spamreport');
    Route::post('blasklisted', [EmailmarketingController::class, 'blasklisted'])->name('blasklisted');
    Route::get('viewblacklisted', [EmailmarketingController::class, 'viewblacklisted'])->name('viewblacklisted');
    Route::get('totalsubscriber', [EmailmarketingController::class, 'totalsubscriber'])->name('totalsubscriber');
    // for activities log
    Route::post('addactivitylog', [EmailmarketingController::class, 'addactivitylog'])->name('addactivitylog');
    Route::get('viewactivitylog', [EmailmarketingController::class, 'viewactivitylog'])->name('viewactivitylog');
    //for api for general and user template
    Route::get('generaltemp', [EmailmarketingController::class, 'generaltemp'])->name('generaltemp');
    Route::post('usertemplate', [EmailmarketingController::class, 'usertemplate'])->name('usertemplate');
    Route::get('viewusertemp', [EmailmarketingController::class, 'viewusertemp'])->name('viewusertemp');
    //for list subscriber email
    Route::get('subscribermail', [EmailmarketingController::class, 'subscribermail'])->name('subscribermail');
    //for recent campaigns
    Route::get('recentcamp', [EmailmarketingController::class, 'recentcamp'])->name('recentcamp');

    Route::post('bulksubscribe', [EmailmarketingController::class, 'bulksubscribe'])->name('bulksubscribe');


       //for tags under subscribe
       Route::get('tagsubscrib', [EmailmarketingController::class, 'tagsubscribe'])->name('tagsubscrib');
       //for tags under unsubscribe
       Route::get('tagunsubscrib', [EmailmarketingController::class, 'tagunsubscrib'])->name('tagunsubscrib');
       //for tags under spamreports
       Route::get('tagspam', [EmailmarketingController::class,'tagspam'])->name('tagspam');
       //for tags under blacklist
       Route::get('tagblacklist', [EmailmarketingController::class, 'tagblacklist'])->name('tagblacklist');
       //route for delete
       Route::delete('deletesubscribe/{id}', [EmailmarketingController::class, 'deletesubscribe'])->name('deletesubscribe');
       Route::delete('deletetags/{id}', [EmailmarketingController::class, 'deletetags'])->name('deletetags');
       Route::delete('deleteblacklist/{id}',[EmailmarketingController::class, 'deleteblacklist'])->name('deleteblacklist');
       Route::delete('deletespam/{id}', [EmailmarketingController::class, 'deletespam'])->name('deletespam');
       Route::delete('deleteunsubscribe/{id}', [EmailmarketingController::class, 'deleteunsubscribe'])->name('deleteunsubscribe');
       Route::delete('deletecamp/{id}', [EmailmarketingController::class, 'deletecamp'])->name('deletecamp');
       Route::delete('deletetempl/{id}', [EmailmarketingController::class, 'deletetempl'])->name('deletetempl');
       //for updates
       Route::put('update-user-info', [AccountController::class, 'updateuserinfo'])->name('updateuserinfo');
       Route::post('upload-profile-pix', [AccountController::class, 'updateprofile'])->name('updateprofile');
       Route::get('viewuserinfo', [AccountController::class, 'viewuserinfo'])->name('viewuserinfo');
       Route::get('viewprofile/{id}', [EmailmarketingController::class, 'viewprofile'])->name('viewprofile');

       Route::post('aimessage', [MessageController::class, 'aimessage'])->name('aimessage');

       Route::get('all-notification', [NotificationController::class, 'allNotification']);
       Route::get('unread-notification', [NotificationController::class, 'unreadonly']);
       Route::patch('mark-as-read-notifications', [NotificationController::class, 'markAsRead']);

       Route::get('pricing', [\App\Http\Controllers\PaymentController::class, 'plans']);

       Route::get('dashboard/main', [DashboardController::class, 'main']);
       Route::get('dashboard/subscriber-growth', [DashboardController::class, 'subscriberGrowth']);

});

