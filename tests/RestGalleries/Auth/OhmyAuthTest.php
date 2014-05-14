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

        $this->assertNotEmpty($data->token['consumer_key']);
        $this->assertNotEmpty($data->token['consumer_secret']);
        $this->assertNotEmpty($data->token['token']);
        $this->assertNotEmpty($data->token['token_secret']);

    }

    /**
     * @dataProvider verifyProvider
     */
    public function testVerifyCredentials($tokenCredentials, $checkUrl)
    {
        $auth = $this->auth;
        $data = $auth::verifyCredentials($tokenCredentials, $checkUrl);

        $this->assertNotEmpty($data->token['consumer_key']);
        $this->assertNotEmpty($data->token['consumer_secret']);
        $this->assertNotEmpty($data->token['token']);
        $this->assertNotEmpty($data->token['token_secret']);

    }

}
