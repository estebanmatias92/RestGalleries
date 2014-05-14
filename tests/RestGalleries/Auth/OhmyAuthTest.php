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
                'consumer_key'    => getenv('FLICKR_KEY'),
                'consumer_secret' => getenv('FLICKR_SECRET'),
                'callback'        => getenv('CALLBACK'),
            ),
            array(
                'request'   => 'https://www.flickr.com/services/oauth/request_token',
            ),
            'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken'
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
    public function testConnect($clientCredentials, $endPoints, $checkUrl)
    {
        $auth = $this->auth;
        $data = $auth::connect($clientCredentials, $endPoints, $checkUrl);
        var_dump($data->tokens);
        $this->assertNotEmpty($data->tokens['consumer_key']);
        $this->assertNotEmpty($data->tokens['consumer_secret']);
        $this->assertEmpty($data->tokens['token']);
        $this->assertEmpty($data->tokens['token_secret']);

    }

    /**
     * @dataProvider verifyProvider
     */
    public function testVerifyCredentials($tokenCredentials, $checkUrl)
    {
        $auth = $this->auth;
        $data = $auth::verifyCredentials($tokenCredentials, $checkUrl);

        $this->assertNotEmpty($data->tokens['consumer_key']);
        $this->assertNotEmpty($data->tokens['consumer_secret']);
        $this->assertNotEmpty($data->tokens['token']);
        $this->assertNotEmpty($data->tokens['token_secret']);

    }

}
