<?php

use Faker\Factory;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->faker = Factory::create();
        $this->faker->addProvider(new Faker\Provider\Miscellaneous($this->faker));
        $this->faker->addProvider(new Faker\Provider\Internet($this->faker));

    }

    public function tearDown()
    {
        Mockery::close();
    }
}
