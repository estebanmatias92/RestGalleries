<?php

use RestGalleries\Http\Guzzle\GuzzleHttp;

class GuzzleHttpTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->url = 'http://api.flickr.com/services/rest/';
    }

    public function testGet()
    {
        $request  = GuzzleHttp::init($this->url);
        $response = $request->GET();

        $this->assertInstanceOf('RestGalleries\\Http\\ResponseAdapter', $response);
        $this->assertEquals(200, $response->getStatusCode());

    }

    public function testPost()
    {
        $request  = GuzzleHttp::init($this->url);
        $response = $request->POST();

        $this->assertInstanceOf('RestGalleries\\Http\\ResponseAdapter', $response);
        $this->assertEquals(200, $response->getStatusCode());

    }

    public function testCache()
    {
        $request  = GuzzleHttp::init($this->url);
        $request->setCache('array');

        $response = $request->GET();

        $this->assertNotEquals(false, stripos($response->getHeaders()['via'], 'GuzzleCache'));

    }

    public function testCacheInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');

        $request  = GuzzleHttp::init($this->url);
        $request->setCache('whatever');

        $response = $request->GET();

    }

    public function testAuth()
    {
        $request  = GuzzleHttp::init($this->url);

        $request->setAuth([
            'consumer_key'    => 'dummy_key',
            'consumer_secret' => 'dummy_secret',
            'token'           => 'dummy_token',
            'token_secret'    => 'dummy_token_secret'
        ]);

        $request->setQuery([
            'method'  => 'flickr.auth.checkToken',
        ]);

        $response = $request->GET();

        $this->assertEquals('Invalid auth token', $response->getBody('xml')->err['msg']);

    }

    public function testAuthInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');

        $request = GuzzleHttp::init($this->url);

        $request->setAuth(['Not Allowed value']);

        $request->GET();

    }

}
