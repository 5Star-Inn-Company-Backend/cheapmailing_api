<?php

use App\Http\Controllers\EmailmarketingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function () {
    return view('test');
});
// Route::post('bulksubscribe', [EmailmarketingController::class, 'bulksubscribe'])->name('bulksubscribe');
Route::get('resetpass', [UserController::class, 'resetpass']);
Route::post('updatepassword', [UserController::class, 'updatepass']);


// Auth::routes();
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('home/{id}', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('dashboard/{id}', function () {
//     return view('dashboard');
// });

// Route::get('addtag', function () {
//     return view('emailmarketing.addtags');
// });

// Route::get('adduser', function () {
//     return view('emailmarketing.adduser');
// });

// Route::get('addcamp', function () {
//     return view('emailmarketing.createcampaign');
// });

// Route::post('createtag', [EmailmarketingController::class, 'createtag'])->name('createtag');
// Route::get('addsubsc', [EmailmarketingController::class, 'addsubsc'])->name('addsubsc');
// Route::post('createsubscriber', [EmailmarketingController::class, 'createsubscriber'])->name('createsubscriber');
// Route::get('viewtag', [EmailmarketingController::class, 'viewtag'])->name('viewtag');
// Route::get('viewsubsc', [EmailmarketingController::class, 'viewsubscriber'])->name('viewsubsc');
// Route::post('createcampaign', [emailmarketingController::class, 'createcampaign'])->name('createcampaign');
// Route::get('viewcamp', [emailmarketingController::class, 'viewcamp'])->name('viewcamp');
// Route::post('inviteuser', [EmailmarketingController::class, 'inviteuser'])->name('inviteuser');
// Route::get('collaboration', [EmailmarketingController::class, 'collaboration'])->name('collaboration');
// Route::get('collaborator', [EmailmarketingController::class, 'collaborators'])->name('collaborators');
//  Route::get('visitacct/{id}', [EmailmarketingController::class, 'visitaact'])->name('visitacct');
// Route::post('visitaccount/{id}', [EmailmarketingController::class, 'visitaccount'])->name('visitaccount');
