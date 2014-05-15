<?php

use RestGalleries\APIs\Flickr\User;

class UserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->responseObject = simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?> <oauth> <token>'.$this->token.'</token> <perms>write</perms> <user nsid="1121451801@N07" username="jamalf" fullname="Jamal F" /> </oauth>');

        parse_str('fullname=Jamal%20Fanaian&oauth_token=72157626318069415-087bfc7b5816092c&oauth_token_secret=a202d1f853ec69de&user_nsid=21207597%40N07&username=jamalfanaian');

        $this->responseObject->tokens['token'] = $oauth_token;
        $this->responseObject->tokens['token_secret'] = $oauth_token_secret;
        $this->responseObject->tokens['consumer_key'] = $this->consumerKey;
        $this->responseObject->tokens['consumer_secret'] = $this->consumerSecret;

        $this->auth = Mockery::mock('RestGalleries\\Auth\\OhmyAuth\\OhmyAuth');

        $this->user = new User($this->auth);

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

        $checkUrl = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';

        $this->auth
            ->shouldReceive('connect')
            ->with($clientCredentials, $authEndPoints, $checkUrl)
            ->once()
            ->andReturn($this->responseObject);

        $user = $this->user->connect($clientCredentials);

        $this->assertNotEmpty($user->id);
        $this->assertNotEmpty($user->realname);
        $this->assertNotEmpty($user->url);
        $this->assertNotEmpty($user->username);
        $this->assertNotEmpty($user->consumer_key);
        $this->assertNotEmpty($user->consumer_secret);
        $this->assertNotEmpty($user->token);
        $this->assertNotEmpty($user->token_secret);

    }

    public function testVerifyCredentials()
    {
        $tokenCredentials = [
            'consumer_key'    => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'token'           => $this->token,
            'token_secret'    => $this->tokenSecret,
        ];

        $checkUrl = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';

        $this->auth
            ->shouldReceive('verifyCredentials')
            ->with($tokenCredentials, $checkUrl)
            ->once()
            ->andReturn($this->responseObject);

        $user = $this->user->verifyCredentials($tokenCredentials);

        $this->assertNotEmpty($user->id);
        $this->assertNotEmpty($user->realname);
        $this->assertNotEmpty($user->url);
        $this->assertNotEmpty($user->username);
        $this->assertNotEmpty($user->consumer_key);
        $this->assertNotEmpty($user->consumer_secret);
        $this->assertNotEmpty($user->token);
        $this->assertNotEmpty($user->token_secret);

    }

}
