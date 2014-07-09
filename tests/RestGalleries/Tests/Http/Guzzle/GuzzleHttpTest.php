<?php namespace RestGalleries\Tests\Http\Guzzle;

use RestGalleries\Http\Guzzle\GuzzleHttp;

class GuzzleHttpTest extends \RestGalleries\Tests\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->url = 'http://api.flickr.com/services/rest/';
    }

    public function testGetRequest()
    {
        $request  = GuzzleHttp::init($this->url);
        $response = $request->GET();

        assertThat($response, is(anInstanceOf('RestGalleries\\Http\\ResponseAdapter')));
        assertThat($response->getStatusCode(), is(equalTo(200)));

    }

    public function testPostRequest()
    {
        $request  = GuzzleHttp::init($this->url);
        $response = $request->POST();

        assertThat($response, is(anInstanceOf('RestGalleries\\Http\\ResponseAdapter')));
        assertThat($response->getStatusCode(), is(equalTo(200)));

    }

    public function testRequestWithCache()
    {
        $request  = GuzzleHttp::init($this->url);
        $request->setCache('array');

        $response = $request->GET();
        $headers  = $response->getHeaders();

        assertThat($headers['via'], containsString('GuzzleCache'));

    }

    public function testCacheInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');

        $request  = GuzzleHttp::init($this->url);
        $request->setCache('whatever');

        $response = $request->GET();

    }

    public function testRequestWithAuth()
    {
        $request  = GuzzleHttp::init($this->url);

        $request->setAuth([
            'consumer_key'    => getenv('FLICKR_KEY'),
            'consumer_secret' => getenv('FLICKR_SECRET'),
            'token'           => 'dummy_token',
            'token_secret'    => 'dummy_token_secret'
        ]);

        $request->setQuery([
            'method'  => 'flickr.auth.checkToken',
        ]);

        $response = $request->GET();
        $body     = $response->getBody();

        assertThat($body->err['msg'], is(equalTo('Invalid auth token')));

    }

    public function testAuthInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');

        $request = GuzzleHttp::init($this->url);

        $request->setAuth(['Not Allowed value']);

        $request->GET();

    }

}
