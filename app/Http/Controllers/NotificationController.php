<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

use DB;
use Auth;
use App\Models\Story;
use App\Models\Sentence;
use App\Models\Tag;
use App\Models\ReplyReceipt;

class NotificationController extends Controller {
	
	public function getReceipts(Request $request) {
		if($request->user()) {
			$receipts = ReplyReceipt::where('user_id', $request->user()->id)->get();
			return Response::json($receipts);
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
	
	public function getUnreadReceipts(Request $request) {
		if($request->user()) {
			$receipts = ReplyReceipt::where('user_id', $request->user()->id)->where('seen', false)->get();
			return Response::json($receipts);
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
	
	public function markReceiptAsRead(Request $request, $id) {
		if($request->user()) {
			$receipt = ReplyReceipt::find($id);
			if($receipt != null) {
				if($receipt->user_id == $request->user()->id) {
					$receipt->seen = true;
					$receipt->save();
					return Response::json(array('message' => 'Receipt cleared.'));
				} else {
					return Response::json(array('message' => 'User mismatch'));
				}
			} else {
				return Response::json(array('message' => 'No receipt found.'));
			}
		} else {
			return Response::json(array('message' => 'Not authorized'));
		}
	}
}
