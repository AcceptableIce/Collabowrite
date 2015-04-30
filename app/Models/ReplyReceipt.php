<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sentence;


class ReplyReceipt extends Model {
	protected $table = 'replies_read';
	
	public function sentence() {
		return Sentence::find($this->sentence_id);
	}
	public function reply() {
		return Sentence::find($this->reply_id);
	}
	
	public function markRead() {
		$this->seen = true;
		$this->save();
	}

}
