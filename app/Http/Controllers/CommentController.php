<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

use Auth;
use App\Models\Comment;
use App\Models\Sentence;
use App\User;

class CommentController extends Controller {
	
	public function getComments(Request $request, $id) {
		$sentence = Sentence::find($id);
		if($sentence != null) {		
			$out = Comment::where('sentence_id', $sentence->id)->get();
			foreach($out as $o) {
				$o["user"] = $o->user();
			}		
			return Response::json($out);
		} else {
			return Response::json(array('message' => 'No sentence found.'));
		}
	}
	
	public function postComment(Request $request, $id) {
		if($request->user()) {
			$sentence = Sentence::find($id);
			if($sentence != null) {
				$comment = new Comment;
				$comment->content = $request->content;
				$comment->user_id = $request->user()->id;
				$comment->sentence_id = $sentence->id;				
				$comment->save();
				return Response::json(array('message' => 'Comment submitted.', 'id' => $comment->id));
			} else {
				return Response::json(array('message' => 'No sentence found.'));
			}
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
	
	public function putComment(Request $request, $id) {
		if($request->user()) {
			$comment = Comment::find($id);
			if($comment != null) {
				$comment->content = $request->content;
				$comment->save();
				return Response::json(array('message' => 'Comment updated.'));
			} else {
				return Response::json(array('message' => 'No sentence found.'));
			}
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
}
