<?php

use RestGalleries\Http\Guzzle\GuzzleHttp;

class GuzzleHttpTest extends TestCase
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

        $this->assertInstanceOf('RestGalleries\\Http\\ResponseAdapter', $response);
        $this->assertEquals(200, $response->getStatusCode());

    }

    public function testPostRequest()
    {
        $request  = GuzzleHttp::init($this->url);
        $response = $request->POST();

        $this->assertInstanceOf('RestGalleries\\Http\\ResponseAdapter', $response);
        $this->assertEquals(200, $response->getStatusCode());

    }

    public function testRequestWithCache()
    {
        $request  = GuzzleHttp::init($this->url);
        $request->setCache('array');

        $response = $request->GET();
        $headers  = $response->getHeaders();

        $this->assertNotEquals(false, stripos($headers['via'], 'GuzzleCache'));

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

        $this->assertEquals('Invalid auth token', $body->err['msg']);

    }

    public function testAuthInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');

        $request = GuzzleHttp::init($this->url);

        $request->setAuth(['Not Allowed value']);

        $request->GET();

    }

}
