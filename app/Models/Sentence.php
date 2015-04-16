<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Sentence extends Model {
	protected $table = 'sentences';
	
	public function children() {
		return Sentence::where('sentence_id', $this->id);
	}
	
	public function comments() {
		return Comment::where('sentence_id', $this->id);
	}
}
