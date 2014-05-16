<?php

use RestGalleries\APIs\Flickr\User;

class UserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        extract($this->clientCredentials = [
            'consumer_key'    => $this->faker->sha1,
            'consumer_secret' => $this->faker->md5,
            'callback'        => $this->faker->url
        ]);

        extract($this->tokenCredentials = [
            'consumer_key'    => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'token'           => $this->faker->sha1,
            'token_secret'    => $this->faker->md5,
        ]);

        $this->authEndPoints = [
            'request'   => 'https://www.flickr.com/services/oauth/request_token',
            'authorize' => 'https://www.flickr.com/services/oauth/authorize',
            'access'    => 'https://www.flickr.com/services/oauth/access_token',
        ];

        $this->checkUrl = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';

        $this->responseObject = simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?> <oauth> <token>'.$token.'</token> <perms>write</perms> <user nsid="1121451801@N07" username="jamalf" fullname="Jamal F" /> </oauth>');

        parse_str('fullname=Jamal%20Fanaian&oauth_token=72157626318069415-087bfc7b5816092c&oauth_token_secret=a202d1f853ec69de&user_nsid=21207597%40N07&username=jamalfanaian');

        $this->responseObject->tokens['token']           = $oauth_token;
        $this->responseObject->tokens['token_secret']    = $oauth_token_secret;
        $this->responseObject->tokens['consumer_key']    = $consumer_key;
        $this->responseObject->tokens['consumer_secret'] = $consumer_secret;

        $this->responseObjectFail = simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?> <rsp stat="fail"> <err code="98" msg="Invalid token" /> </rsp>');


        $this->auth = Mockery::mock('RestGalleries\\Auth\\OhmyAuth\\OhmyAuth');

        $this->user = new User($this->auth);

    }

    public function testConnect()
    {
        $this->auth
            ->shouldReceive('connect')
            ->with($this->clientCredentials, $this->authEndPoints, $this->checkUrl)
            ->once()
            ->andReturn($this->responseObject);

        $user = $this->user->connect($this->clientCredentials);

        $this->assertNotEmpty($user->id);
        $this->assertNotEmpty($user->realname);
        $this->assertNotEmpty($user->url);
        $this->assertNotEmpty($user->username);
        $this->assertNotEmpty($user->consumer_key);
        $this->assertNotEmpty($user->consumer_secret);
        $this->assertNotEmpty($user->token);
        $this->assertNotEmpty($user->token_secret);

    }

    /**
     * @expectedException RestGalleries\Exception\AuthException
     */
    public function testConnectFail()
    {
        $this->auth
            ->shouldReceive('connect')
            ->with($this->clientCredentials, $this->authEndPoints, $this->checkUrl)
            ->once()
            ->andReturn($this->responseObjectFail);

        $user = $this->user->connect($this->clientCredentials);
    }

    public function testVerifyCredentials()
    {
        $this->auth
            ->shouldReceive('verifyCredentials')
            ->with($this->tokenCredentials, $this->checkUrl)
            ->once()
            ->andReturn($this->responseObject);

        $user = $this->user->verifyCredentials($this->tokenCredentials);

        $this->assertNotEmpty($user->id);
        $this->assertNotEmpty($user->realname);
        $this->assertNotEmpty($user->url);
        $this->assertNotEmpty($user->username);
        $this->assertNotEmpty($user->consumer_key);
        $this->assertNotEmpty($user->consumer_secret);
        $this->assertNotEmpty($user->token);
        $this->assertNotEmpty($user->token_secret);

    }

    /**
     * @expectedException RestGalleries\Exception\AuthException
     */
    public function testVerifyCredentialsFail()
    {
        $this->auth
            ->shouldReceive('verifyCredentials')
            ->with($this->tokenCredentials, $this->checkUrl)
            ->once()
            ->andReturn($this->responseObjectFail);

        $user = $this->user->verifyCredentials($this->tokenCredentials, $this->checkUrl);

    }

}
