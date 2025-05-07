<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TagsController;
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

Route::group(['prefix' => 's2s', 'middleware' => 'auth:api'], function (){

    Route::post('create-tags', [TagsController::class, 'createtags'])->name('createtags');
    Route::get('view-tags', [TagsController::class, 'viewtags'])->name('viewtags');
    Route::get('edittags/{id}', [TagsController::class, 'edittags'])->name('edittags');
    Route::put('updatetags/{id}', [TagsController::class, 'updatetags'])->name('updatetags');
    Route::delete('deletetags/{id}', [TagsController::class, 'deletetags'])->name('deletetags');

    Route::post('add-subscriber', [SubscriberController::class, 'addsubscrib'])->name('addsubscrib');
    Route::get('view-subscribers', [SubscriberController::class, 'viewsubscribers'])->name('viewsubscrib');
    Route::get('unsubscribe', [SubscriberController::class, 'viewunsubscribers'])->name('unsubscribe');
    Route::post('subscribers/bulk-upload', [SubscriberController::class, 'bulkUpload']);
    Route::delete('deletesubscribe/{id}', [SubscriberController::class, 'deleteSubscriber']);

    Route::post('blasklisted', [SubscriberController::class, 'blasklisted'])->name('blasklisted');
    Route::get('viewblacklisted', [SubscriberController::class, 'viewblacklisted'])->name('viewblacklisted');

    Route::post('create-campaigns', [emailmarketingController::class, 'createcampaigns']);
    Route::get('view-campaigns', [emailmarketingController::class, 'viewcamps'])->name('viewcamps');

    Route::get('list_spamreport', [EmailmarketingController::class, 'list_spamreport'])->name('list_spamreport');

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

       Route::delete('deleteblacklist/{id}',[EmailmarketingController::class, 'deleteblacklist'])->name('deleteblacklist');
       Route::delete('deletespam/{id}', [EmailmarketingController::class, 'deletespam'])->name('deletespam');
       Route::delete('deleteunsubscribe/{id}', [EmailmarketingController::class, 'deleteunsubscribe'])->name('deleteunsubscribe');
       Route::delete('deletetempl/{id}', [EmailmarketingController::class, 'deletetempl'])->name('deletetempl');
       //for updates
       Route::put('update-user-info', [AccountController::class, 'updateuserinfo'])->name('updateuserinfo');
       Route::post('upload-profile-pix', [AccountController::class, 'updateprofile'])->name('updateprofile');
       Route::get('viewuserinfo', [AccountController::class, 'viewuserinfo'])->name('viewuserinfo');
       Route::get('viewprofile/{id}', [EmailmarketingController::class, 'viewprofile'])->name('viewprofile');

       Route::get('all-notification', [NotificationController::class, 'allNotification']);
       Route::get('unread-notification', [NotificationController::class, 'unreadonly']);
       Route::patch('mark-as-read-notifications', [NotificationController::class, 'markAsRead']);

       Route::get('pricing', [\App\Http\Controllers\PaymentController::class, 'plans']);

       Route::get('dashboard/main', [DashboardController::class, 'main']);
       Route::get('dashboard/subscriber-growth', [DashboardController::class, 'subscriberGrowth']);

});

