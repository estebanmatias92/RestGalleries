<?php

use RestGalleries\APIs\Flickr\User;

class UserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->responseObject = simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?> <oauth> <token>'.$this->token.'</token> <perms>write</perms> <user nsid="1121451801@N07" username="jamalf" fullname="Jamal F" /> </oauth>');

        $credentials = parse_url('fullname=Jamal%20Fanaian&oauth_token=72157626318069415-087bfc7b5816092c&oauth_token_secret=a202d1f853ec69de&user_nsid=21207597%40N07&username=jamalfanaian');

        foreach ($credentials as $key => $value) {
            $this->responseObject->credentials[$key] = $value;
        }

        $this->urlCheck = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';

        $this->http = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleHttp');

        $this->auth = Mockery::mock('RestGalleries\\Auth\\OhmyAuth\\OhmyAuth');

        $this->user = new User($this->http, $this->auth);

    }

    public function testConnect()
    {
        $clientCredentials = [
            'consumer_key'    => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'callback'        => $this->faker->url
        ];

        $authEndPoints = [
            'request'   => 'https://www.flickr.com/services/oauth/request_token',
            'authorize' => 'https://www.flickr.com/services/oauth/authorize',
            'access'    => 'https://www.flickr.com/services/oauth/access_token',
        ];

        $urlCheck = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';

        $this->auth
            ->shouldReceive('connect')
            ->with($clientCredentials, $authEndPoints, $urlCheck)
            ->once()
            ->andReturn($this->responseObject);

        $user = $this->user->connect($clientCredentials);

        foreach ($this->credentialsOAuth1 as $value) {
            $this->assertNotEmpty($user->{$value});
        }

    }

    public function testVerifyCredentials()
    {
        $tokenCredentials = [
            'consumer_key'    => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'token'           => $this->token,
            'token_secret'    => $this->tokenSecret,
        ];

        $urlCheck = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';

        $this->auth
            ->shouldReceive('verifyCredentials')
            ->with($tokenCredentials, $urlCheck)
            ->once()
            ->andReturn($this->responseObject);

        $user = $this->user->verifyCredentials($tokenCredentials);

        foreach ($this->credentialsOAuth1 as $value) {
            $this->assertNotEmpty($user->{$value});
        }

    }

}
