<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

use DB;
use Auth;
use App\Models\Story;
use App\Models\Sentence;
use App\Models\Tag;
use App\Models\ReplyReceipt;

class StoryController extends Controller {

	public function toggleLock(Request $request, $id) {
		if($request->user()) {
			$story = Story::find($id);
			if($story != null) {
				if($story->owner()->id == $request->user()->id) {
					$story->locked = !$story->locked;
					$story->save();
					return Response::json(array('message' => 'Lock toggled.'));
				} else {
					return Response::json(array('message' => 'Ownership mismatch'));
				}
			} else {
				return Response::json(array('message' => 'No story found.'));
			}
		} else {
			return 'Not authorized';
		}
	}
	
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
	public function postTag(Request $request, $id) {
		if($request->user()) {
			$story = Story::find($id);
			if($story != null) {
				if($story->owner()->id == $request->user()->id) {
					$tag = new Tag;
					$tag->story_id = $id;
					$tag->value = $request->value;
					$tag->save();
					return Response::json(array('message' => 'Tag added.', 'id' => $tag->id));
				} else {
					return Response::json(array('message' => 'Ownership mismatch'));
				}
			} else {
				return Response::json(array('message' => 'No story found.'));
			}
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
	
	public function postReply(Request $request, $id) {
		if($request->user()) {
			$story = Story::find($id);
			if($story != null) {
				//Only allow replies to unlocked stories
				if(!$story->locked) {
					//Add check to see if reply to this level has already happened.
					$sentence = new Sentence;
					$sentence->user_id = $request->user()->id;
					$sentence->story_id = $id;
					$sentence->sentence_id = $request->sentence_id;
					$sentence->content = $request->reply;
					$sentence->save();
					
					$parent_id = Sentence::find($request->sentence_id)->owner()->id;
					if($parent_id != $request->user()->id) {
						$reply = new ReplyReceipt;
						$reply->user_id = $parent_id;
						$reply->sentence_id = $request->sentence_id;
						$reply->reply_id = $sentence->id;
						$reply->seen = false;
						$reply->save();
					}
					
					return Response::json(array('message' => 'Reply submitted.', 'id' => $sentence->id));
				} else {
					return Response::json(array('message' => 'Story locked'));
				}
			} else {
				return Response::json(array('message' => 'No story found.'));
			}
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
	
	public function search(Request $request) {
		$searchTerms = explode(',', $request->input('q'));

	    $query = DB::table('sentences');
	
	    foreach($searchTerms as $term) {
	        $query->orWhere('content', 'LIKE', '%'. $term .'%');
	    }
	
	    $subresults = $query->select('story_id', DB::raw('count(*) as total'))->groupBy('story_id')->orderBy('total', 'DESC')->get();
	    //Add tag results
	    foreach(Tag::where('value', 'LIKE', '%'. $term .'%')->get() as $t) {
		    $subObj = new \stdClass();
		    $subObj->story_id = $t->story()->id;
		    $subresults[] = $subObj;
		}
	    $results = array();
	    foreach($subresults as $s) {
		    $story = Story::find($s->story_id);
		    if($story != null) {
			    $story["root"] = $story->getRoot()->get();
			    $results[] = $story;
			}
	    }
	    return Response::json($results);
	}
}
