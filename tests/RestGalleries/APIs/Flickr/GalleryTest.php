<?php

use RestGalleries\APIs\Flickr\Gallery;

class GalleryTest extends TestCase
{
    public function testAll()
    {
        $gallery = new FlickrGalleryAllStub;

        $gallery->setCredentials(['token_credentials']);
        $gallery->setCache('cache-system', ['cache_path']);

        $galleries = $gallery->all();

        assertThat($galleries, is(objectValue()));

    }

}

class FlickrGalleryStub extends RestGalleries\APIs\Flickr\Gallery
{
    public function newHttp(RestGalleries\Http\HttpAdapter $http = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleHttp');
        $mock->shouldReceive('init')
            ->with('http://api.flickr.com/services/rest/')
            ->atLeast()
            ->andReturn($mock);

        $mock->shouldReceive('setAuth')
            ->with(['token_credentials'])
            ->atLeast();

        $mock->shouldReceive('setCache')
            ->with('cache-system', ['cache_path'])
            ->atLeast();

        return parent::newHttp($mock);

    }

    public function newPhoto(RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = Mockery::mock('RestGalleries\\APIs\\Flickr\\Photo');

        return $mock;

    }

}

class FlickrGalleryAllStub extends FlickrGalleryStub
{
    public function newHttp(RestGalleries\Http\HttpAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/gallery/';
        $mock         = parent::newHttp();
        $mockResponse = Mockery::mock('RestGalleries\\Http\\Response');

        $mock->shouldReceive('setQuery')
            ->with(typeOf('array'))
            ->once();

        $mock->shouldReceive('GET')
            ->once()
            ->andReturn($mockResponse);

        $responseFile = $responsesDir . 'flickr-photosets-getlist.json';
        $responseBody = json_decode(file_get_contents($responseFile), true);

        $mockResponse->shouldReceive('getBody')
            ->with('array')
            ->atMost()
            ->times(1)
            ->andReturn($responseBody);

        $responseFile = $responsesDir . 'flickr-photosets-getinfo.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mockResponse->shouldReceive('getBody')
            ->atMost()
            ->times(1)
            ->andReturn($responseBody);

        return $mock;

    }

    public function newPhoto(RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = parent::newPhoto();
        $mock->shouldReceive('all')
            ->with('72157633782247768')
            ->once()
            ->andReturn(['photo_array']);

        return $mock;

    }

}
