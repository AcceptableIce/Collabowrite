<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

use Auth;
use App\Models\Comment;
use App\Models\Sentence;
use App\User;

class UserController extends Controller {
	
	public function updateProfile(Request $request, $id) {
		if($request->user()) {
			$user = User::find($id);
			if($user != null) {		
				if($user->id == $request->user()->id) {
					$profile = $user->profile();
					switch($request->field) {
						case "twitter":
							$profile->twitter = $request->content;
							break;
						case "description";
							$profile->description = $request->content;
							break;
						case "website":
							$profile->website = $request->content;
							break;
					}
					$profile->save();
					return Response::json(array('message' => 'Update completed.'));
				} else {
					return Response::json(array('message' => 'User authorization mismatch.'));
				}
			} else {
				return Response::json(array('message' => 'No sentence found.'));
			}
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
}
