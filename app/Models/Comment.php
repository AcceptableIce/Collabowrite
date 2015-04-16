<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Comment extends Model {
	protected $table = 'comments';
	
	public function sentence() {
		return Sentence::find($this->sentence_id);
	}
	
	public function user() {
		return User::find($this->user_id);
	}
}
