<?php

use Faker\Factory;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->faker = Factory::create();
        $this->faker->addProvider(new Faker\Provider\Miscellaneous($this->faker));
        $this->faker->addProvider(new Faker\Provider\Internet($this->faker));

        $this->consumerKey    = $this->faker->sha1;
        $this->consumerSecret = $this->faker->md5;
        $this->token          = 't-'.$this->faker->sha1;
        $this->tokenSecret    = 'ts-'.$this->faker->md5;

        $this->credentialsOAuth1 = ['id', 'url', 'username', 'realname', 'token', 'token_secret'];

        $this->credentialsOAuth2 = ['id', 'url', 'username', 'realname', 'access_token', 'token_type'];

    }

    public function tearDown()
    {
        Mockery::close();
    }
}
