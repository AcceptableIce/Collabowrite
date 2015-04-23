<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
	protected $table = 'tags';
	
	public function story() {
		return Story::find($this->story_id);
	}
}
