<?php

use Faker\Factory;
use Hamcrest\MatcherAssert;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Collects assertions performed by Hamcrest matchers during the test.
     *
     * @throws Exception
     */
    public function runBare()
    {
        MatcherAssert::resetCount();

        try {
            parent::runBare();
        }
        catch (\Exception $exception) {}

        $this->addToAssertionCount(MatcherAssert::getCount());

        if (isset($exception)) {
            throw $exception;
        }

    }

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
