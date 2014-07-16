<?php namespace RestGalleries\Tests\Auth\OhmyAuth;

use RestGalleries\Auth\OhmyAuth\OhmyAuth;

class OhmyAuthTest extends \RestGalleries\Tests\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->auth = new OhmyAuth;
    }

    public function connectProvider()
    {
        $flickr = [
            [
                'consumer_key'    => getenv('FLICKR_KEY'),
                'consumer_secret' => getenv('FLICKR_SECRET'),
                'callback'        => getenv('CALLBACK'),
            ],
            [
                'request' => 'https://www.flickr.com/services/oauth/request_token',
            ],
            'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken',
        ];

        return [
            $flickr,
        ];

    }

    public function verifyProvider()
    {
        $flickr = [
            [
                'consumer_key'    => getenv('FLICKR_KEY'),
                'consumer_secret' => getenv('FLICKR_SECRET'),
                'token'           => 'dummy_token',
                'token_secret'    => 'dummy_token_secret'
            ],
            'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken',
        ];

        return [
            $flickr,
        ];

    }

    /**
     * @dataProvider connectProvider
     */
    public function testConnect($clientCredentials, $endPoints, $checkUrl)
    {
        $auth = $this->auth;
        $data = $auth::connect($clientCredentials, $endPoints, $checkUrl);

        assertThat($data->tokens['consumer_key'], is(not(nullOrEmptyString())));
        assertThat($data->tokens['consumer_secret'], is(not(nullOrEmptyString())));
        assertThat($data->tokens['token'], is(not(nullOrEmptyString())));
        assertThat($data->tokens['token_secret'], is(not(nullOrEmptyString())));
    }

    public function testConnectInvalidKeys()
    {
        $this->setExpectedException('RestGalleries\\Exception\\AuthException', 'Credentials keys are invalid');

        $clientCredentials = [
            'client_id' => $this->faker->md5,
            'consumer_secret' => $this->faker->sha1,
            'callback' => $this->faker->url
        ];

        $authEndPoints = [
            'whatever' => $this->faker->url,
            'authorize' => $this->faker->url,
            'access' => $this->faker->url
        ];

        $checkUrl = $this->faker->url;

        $auth = $this->auth;
        $data = $auth::connect($clientCredentials, $authEndPoints, $checkUrl);

    }

    /**
     * @dataProvider verifyProvider
     */
    public function testVerifyCredentials($tokenCredentials, $checkUrl)
    {
        $auth = $this->auth;
        $data = $auth::verifyCredentials($tokenCredentials, $checkUrl);

        assertThat($data->tokens['consumer_key'], is(not(nullOrEmptyString())));
        assertThat($data->tokens['consumer_secret'], is(not(nullOrEmptyString())));
        assertThat($data->tokens['token'], is(not(nullOrEmptyString())));
        assertThat($data->tokens['token_secret'], is(not(nullOrEmptyString())));

    }

    public function testVerifyCredentialsInvalidKeys()
    {
        $this->setExpectedException(
          'RestGalleries\\Exception\\AuthException', 'Credentials keys are invalid'
        );

        $tokenCredentials = [
            'consumer_key' => $this->faker->md5,
            'consumer_secret' => $this->faker->sha1,
            'access_token' => $this->faker->md5
        ];

        $checkUrl = $this->faker->url;

        $auth = $this->auth;
        $data = $auth::verifyCredentials($tokenCredentials, $checkUrl);

    }

}
