<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\User;


class Story extends Model {
	protected $table = 'stories';
	
	public function tags() {
		return $this->hasMany('App\Models\Tag');
	}
	public function getRoot() {
		return $this->hasMany('App\Models\Sentence')->where('sentence_id', 0);
	}
	
	public function owner() {
		return User::find($this->user_id);
	}
	
	public function buildTree() {
		$rootVal = $this->getRoot()->first();
		$root = array("content" => $rootVal->content, "selected" => 0, "id" => $rootVal->id, "comment_count" => $rootVal->comments()->count(), "children" => array());
		$this->buildTreeLevel($rootVal, $root);
		return $root;
	}
	
	public function buildTreeLevel($item, &$parent) {
		foreach($item->children()->get() as $c) {
			$newTreeItem = array("content" => $c->content, "selected" => 0, "id" => $c->id, "comment_count" => $c->comments()->count(), "children" => array());
			$this->buildTreeLevel($c, $newTreeItem);
			$parent["children"][] = $newTreeItem;
		}
	}

}
