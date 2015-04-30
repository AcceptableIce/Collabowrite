`<?php
use App\User;
use App\Models\Story;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\ReplyReceipt;
use Symfony\Component\DomCrawler\Crawler;

class ExampleTest extends TestCase {

	/**
	 * Tests redirection to the login page when not logged in (default).
	 *
	 * @return void
	 */
	public function testHomepageRedirect() {
		$response = $this->call('GET', '/');
		// should redirect to the login page
		$this->assertRedirectedTo('/auth/login');
	}


	/**
	 * Logs in and attempts to load the home page.
	 *
	 * @return void
	 */
	public function testLoggedInHomepage() {
		$user = new User(['name' => 'shawn']);
		$this->be($user);

		$response = $this->call('GET', '/');
		$this->assertResponseOk();
	}

	/**
	 * Logs in and attempts to post a new prompt.
	 *
	 * @return void
	 */
	public function testNewStory() {
		Auth::loginUsingId(2);
		// get the homepage
		$response = $this->call('GET', '/');
		$this->assertResponseOk();
		$html = $response->getContent();
		// extract the csrf token

		$crawler = new Crawler($html);
		$token = $crawler->filter('input[name="_token"]')->attr('value');
		
		$response = $this->call('POST', '/api/v1/story', ['sentence' => 'Test!', '_token' => $token]);
		// if successful, should redirect to the story page
		// pull out the redirect
		$crawler = new Crawler($response->getContent());
		$redirectUrl = $crawler->filter('a')->attr('href');
		// pull out the story id
		preg_match('/([0-9]+)/', $redirectUrl, $matches);
		$newStoryId = $matches[0];
		$this->assertRedirectedTo('/story/'.$newStoryId);

		// make sure the content actually got posted
		$response = $this->call('GET', '/story/'.$newStoryId);
		// there isn't currently a decent way to actually get the data that was returned, so just make sure it's ok
		$this->assertResponseOk();

		// delete it
		Story::find($newStoryId)->delete();
	}
	
	/**
	 * Logs in and attempts to post a new comment.
	 *
	 * @return void
	 */
	public function testNewComment() {
		Auth::loginUsingId(2);
		$response = $this->call('GET', '/');
		$this->assertResponseOk();
		$html = $response->getContent();
		// extract the csrf token

		$crawler = new Crawler($html);
		$token = $crawler->filter('input[name="_token"]')->attr('value');
		
		$response = $this->call('POST', '/api/v1/sentence/1/comment', ['content' => 'Test!', '_token' => $token]);
		$newCommentId = json_decode($response->getContent())->id;
		// there isn't currently a decent way to actually get the data that was returned, so just make sure it's ok
		$this->assertNotEquals($newCommentId,null);

		// delete it
		Comment::find($newCommentId)->delete();
	}
	
	/**
	 * Logs in and attempts to add a tag to a story.
	 *
	 * @return void
	 */
	public function testNewTag() {
		Auth::loginUsingId(1);
		$response = $this->call('GET', '/');
		$this->assertResponseOk();
		$html = $response->getContent();
		// extract the csrf token

		$crawler = new Crawler($html);
		$token = $crawler->filter('input[name="_token"]')->attr('value');
		
		$response = $this->call('POST', '/api/v1/story/1/tag', ['value' => 'Test!', '_token' => $token]);
		$newTagId = json_decode($response->getContent())->id;
		// there isn't currently a decent way to actually get the data that was returned, so just make sure it's ok
		$this->assertNotEquals($newTagId, null);

		// delete it
		Tag::find($newTagId)->delete();
	}

	/**
	 * Logs in and attempts to create, read, and mark a notification as read.
	 *
	 * @return void
	 */
	public function testReadReceipts() {
		Auth::loginUsingId(1);

		$response = $this->call('GET', '/');
		$this->assertResponseOk();
		$html = $response->getContent();
		// extract the csrf token

		$crawler = new Crawler($html);
		$token = $crawler->filter('input[name="_token"]')->attr('value');

		$receipt = new ReplyReceipt();
		// because it would make too much sense to put this in the constructor
		$receipt->user_id = 1;
		$receipt->sentence_id = 1;
		$receipt->reply_id = 1;
		$receipt->seen = false;

		$receipt->save();
		$receipt->update();

		$this->assertFalse($receipt->seen);

		$response = $this->call('GET', '/api/v1/receipts/unread');
		$unread = json_decode($response->getContent());
		$found = false;
		// find a read receipt matching the one we just added
		foreach ($unread as $response_receipt) {
			if($response_receipt->user_id == 1 && $response_receipt->sentence_id == 1 && $response_receipt->reply_id == 1) {
				$found = true;
				// "read" the receipts
				$response = $this->call('POST', '/api/v1/receipt/'.$response_receipt->id.'/read', ['_token' => $token]);
				// make sure the receipts are "read" properly
				$this->assertEquals($response->getContent(), '{"message":"Receipt cleared."}');
			}
		}
		$this->assertTrue($found);

		// make sure we have no unread notifications now
		$response = $this->call('GET', '/api/v1/receipts/unread');
		$this->assertEquals($response->getContent(), '[]');
	}
}
