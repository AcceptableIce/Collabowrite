<?php
use App\User;
use App\Models\Story;
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
}
