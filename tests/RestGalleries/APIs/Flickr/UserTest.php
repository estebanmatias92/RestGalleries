<?php

use Faker\Factory;
use RestGalleries\APIs\Flickr\User;

class UserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create();
        $this->faker->addProvider(new Faker\Provider\Miscellaneous($this->faker));
        $this->faker->addProvider(new Faker\Provider\Internet($this->faker));

        $this->consumerKey    = $this->faker->sha1;
        $this->consumerSecret = $this->faker->md5;
        $this->token          = 't-'.$this->faker->sha1;
        $this->tokenSecret    = 'ts-'.$this->faker->md5;

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

        $this->auth
            ->shouldReceive('connect')
            ->with($clientCredentials, $authEndPoints)
            ->once()
            ->andReturn([
                'oauth_token' => 'succeful auth'
            ]);

        $credentials = $this->user->connect($clientCredentials, $authEndPoints);

        $this->assertArrayHasKey('oauth_token', $credentials);

    }

    public function testGetCredentials()
    {
        $tokenCredentials = [
            'consumer_key'    => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'token'           => $this->token,
            'token_secret'    => $this->tokenSecret,
        ];

        $urlCheck = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';

        $xmlObject = simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?> <oauth> <token>'.$this->token.'</token> <perms>write</perms> <user nsid="1121451801@N07" username="jamalf" fullname="Jamal F" /> </oauth>');

        $this->auth
            ->shouldReceive('verifyCredentials')
            ->with($tokenCredentials, $urlCheck)
            ->once()
            ->andReturn($xmlObject);

        $user = $this->user->getCredentials($tokenCredentials);

        $this->assertInstanceOf('RestGalleries\\APIs\\Flickr\\User', $user);
        $this->assertNotEmpty($user->token);

    }

}
