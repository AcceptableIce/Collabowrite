<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepliesReadTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('replies_read', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('sentence_id');
			$table->integer('reply_id');
			$table->boolean('seen');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('replies_read');
	}

}
