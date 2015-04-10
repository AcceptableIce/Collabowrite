<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\User;


class Story extends Model {
	protected $table = 'stories';
	
	public function getRoot() {
		return $this->hasMany('App\Models\Sentence')->where('sentence_id', 0);
	}
	
	public function owner() {
		return User::find($this->user_id);
	}
	
	public function buildTree() {
		$rootVal = $this->getRoot()->first();
		$root = array("content" => $rootVal->content, "selected" => 0, "id" => $rootVal->id, "children" => array());
		$this->buildTreeLevel($rootVal, $root);
		return $root;
	}
	
	public function buildTreeLevel($item, &$parent) {
		foreach($item->children()->get() as $c) {
			$newTreeItem = array("content" => $c->content, "selected" => 0, "id" => $c->id, "children" => array());
			$this->buildTreeLevel($c, $newTreeItem);
			$parent["children"][] = $newTreeItem;
		}
	}

}
