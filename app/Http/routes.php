<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\Models\Story;
use App\User;

Route::get('/', 'HomeController@index');

Route::get('/story/{id}', array('as' => 'story', function($id) {
	return view('story')->with('story', Story::find($id));
}));

Route::get('/user/{id}', array('as' => 'user', function($id) {
	return view('user')->with('user', User::find($id));
}));

//API Routes
Route::group(['prefix' => 'api/v1'], function() {
	Route::post('story', 'StoryController@postStory');
	Route::post('story/{id}/reply', 'StoryController@postReply');
	Route::post('story/{id}/tag', 'StoryController@postTag');
	Route::post('story/{id}/lock', 'StoryController@toggleLock');
	Route::get('sentence/{id}/comments', 'CommentController@getComments');
	Route::post('sentence/{id}/comment', 'CommentController@postComment');
	Route::put('comment/{id}', 'CommentController@putComment');
	Route::post('user/{id}/profile', 'UserController@updateProfile');
	Route::get('search', 'StoryController@search');
	Route::get('receipts', 'NotificationController@getReceipts');
	Route::get('receipts/unread', 'NotificationController@getUnreadReceipts');
	Route::post('receipt/{id}/read', 'NotificationController@markReceiptAsRead');
});


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
