<?php

use RestGalleries\Factory;

class FactoryTest extends TestCase
{
    public function testMakeGallery()
    {
        $serviceGallery = Factory::makeGallery('Flickr');

        assertThat($serviceGallery, is(objectValue()));

    }

    public function testMakeUser()
    {
        $serviceUser = Factory::makeUser('Flickr');

        assertThat($serviceUser, is(objectValue()));

    }

    public function testInvalidServiceName()
    {
        $this->setExpectedException('RestGalleries\\Exception\\ApiNotFoundException');

        $result = Factory::make('InvalidApi');

    }

}
