<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

use Auth;
use App\Models\Story;
use App\Models\Sentence;

class StoryController extends Controller {

	public function postStory(Request $request) {
		if($request->user()) {
			$user_id = $request->user()->id;
			$story = new Story;
			$story->user_id = $user_id;
			$story->save();
			
			$story_id = $story->id;
			
			$sentence = new Sentence;
			$sentence->user_id = $user_id;
			$sentence->story_id = $story_id;
			$sentence->sentence_id = 0;
			$sentence->content = $request->sentence;
			$sentence->save();
			
			return redirect()->route('story', [$story_id]);
		} else {
			return 'Not authorized';
		}
	}
	
	public function postReply(Request $request, $id) {
		if($request->user()) {
			$story = Story::find($id);
			if($story != null) {
				//Add check to see if reply to this level has already happened.
				$sentence = new Sentence;
				$sentence->user_id = $request->user()->id;
				$sentence->story_id = $id;
				$sentence->sentence_id = $request->sentence_id;
				$sentence->content = $request->reply;
				$sentence->save();
				return Response::json(array('message' => 'Reply submitted.', 'id' => $sentence->id));
			} else {
				return Response::json(array('message' => 'No story found.'));
			}
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
}
