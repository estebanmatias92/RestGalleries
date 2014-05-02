<?php

class FactoryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mock = Mockery::mock('RestGalleries\\Factory')->makePartial();
    }

    public function testCanCreateObject()
    {
        $this->mock
            ->shouldReceive('createApi')
            ->once()
            ->andReturn(Mockery::mock('Flickr'));

        $api = $this->mock->fire('flickr');

        $this->assertInstanceOf('Flickr', $api);

    }

    public function testCanNotCreateObject()
    {
        $this->mock
            ->shouldReceive('createApi')
            ->never();

        $this->setExpectedException('RestGalleries\\Exception\\ApiNotFoundException');

        $api = $this->mock->fire('invalid api');
    }

}
