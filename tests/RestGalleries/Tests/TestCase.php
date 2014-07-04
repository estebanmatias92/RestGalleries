<?php namespace RestGalleries\Tests;

use Faker;
use Hamcrest;
use Mockery;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Collects assertions performed by Hamcrest matchers during the test.
     *
     * @throws Exception
     */
    public function runBare()
    {
        Hamcrest\MatcherAssert::resetCount();

        try {
            parent::runBare();
        }
        catch (\Exception $exception) {}

        $this->addToAssertionCount(Hamcrest\MatcherAssert::getCount());

        if (isset($exception)) {
            throw $exception;
        }

    }

    public function setUp()
    {
        $this->faker = Faker\Factory::create();
        $this->faker->addProvider(new Faker\Provider\Miscellaneous($this->faker));
        $this->faker->addProvider(new Faker\Provider\Internet($this->faker));

    }

    public function tearDown()
    {
        Mockery::close();
    }
}
