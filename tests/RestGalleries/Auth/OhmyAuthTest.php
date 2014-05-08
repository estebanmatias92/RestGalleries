<?php

use RestGalleries\Auth\OhmyAuth\OhmyAuth;

class OhmyAuthTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->auth = new OhmyAuth;
    }

    public function connectProvider()
    {
        $flickr = array(
            array(
                'key'      => getenv('FLICKR_KEY'),
                'secret'   => getenv('FLICKR_SECRET'),
                'callback' => getenv('CALLBACK'),
            ),
            array(
                'request'   => 'https://www.flickr.com/services/oauth/request_token',
            ),
        );

        return array(
            $flickr,
        );

    }

    public function verifyProvider()
    {
        $flickr = array(
            array(
                'consumer_key'    => getenv('FLICKR_KEY'),
                'consumer_secret' => getenv('FLICKR_SECRET'),
                'token'           => 'dummy_token',
                'token_secret'    => 'dummy_token_secret'
            ),
            'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken'
        );

        return array(
            $flickr,
        );
    }

    /**
     * @dataProvider connectProvider
     */
    public function testConnect($clientCredentials, $endPoints)
    {
        $auth        = $this->auth;
        $credentials = $auth::connect($clientCredentials, $endPoints);

        $this->assertTrue(is_string($credentials));

    }

    /**
     * @dataProvider verifyProvider
     */
    public function testVerifyCredentials($tokenCredentials, $uri)
    {
        $auth = $this->auth;
        $data = $auth::verifyCredentials($tokenCredentials, $uri);

        $this->assertTrue(is_object($data));

    }

}
