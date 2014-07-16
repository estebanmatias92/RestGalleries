<?php namespace RestGalleries\Tests\APIs\Flickr;

use Mockery;

class GalleryTest extends \RestGalleries\Tests\TestCase
{
    public function testAll()
    {
        $model = new FlickrGalleryAllStub;

        $galleries = $model->all();

        assertThat($galleries, is(objectValue()));

    }

    public function testAllEmptyReturn()
    {
        $model = new FlickrGalleryAllEmptyReturnStub;

        $galleries = $model->all();

        assertThat($galleries, is(nullValue()));

    }

    public function testFind()
    {
        $model = new FlickrGalleryFindStub;

        $gallery = $model->find('72157633782247768');

        assertThat($gallery, is(objectValue()));

    }

    public function testFindNotFound()
    {
        $model = new FlickrGalleryFindNotFoundStub;

        $gallery = $model->find('some-invalid-gallery-id');

        assertThat($gallery, is(nullValue()));

    }

}

class FlickrGalleryStub extends \RestGalleries\APIs\Flickr\Gallery
{
    protected $cache  = [
        'file_system' => 'cache-name',
        'path' => ['cache_path'],
    ];

    protected $credentials = ['token_credentials'];

    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleHttp');
        $mock->shouldReceive('init')
            ->with('http://api.flickr.com/services/rest/')
            ->atMost()
            ->times(3)
            ->andReturn($mock);

        $mock->shouldReceive('setAuth')
            ->with(['token_credentials'])
            ->atMost()
            ->times(3);

        $mock->shouldReceive('setCache')
            ->with('cache-name', ['cache_path'])
            ->atMost()
            ->times(3);

        return parent::newHttp($mock);

    }

    public function newPhoto(\RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = Mockery::mock('RestGalleries\\APIs\\Flickr\\Photo');

        return $mock;

    }

}

class FlickrGalleryAllStub extends FlickrGalleryStub
{
    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
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

    public function newPhoto(\RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = parent::newPhoto();
        $mock->shouldReceive('all')
            ->with('72157633782247768')
            ->once()
            ->andReturn(['photo_array']);

        return $mock;

    }

}

class FlickrGalleryAllEmptyReturnStub extends FlickrGalleryStub
{
    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
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

        $responseFile = $responsesDir . 'flickr-photosets-getlist-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile), true);

        $mockResponse->shouldReceive('getBody')
            ->with('array')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

    public function newPhoto(\RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = parent::newPhoto();
        $mock->shouldReceive('all')
            ->with('72157633782247768')
            ->once()
            ->andReturn(['photo_array']);

        return $mock;

    }

}


class FlickrGalleryFindStub extends FlickrGalleryStub
{
    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
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

        $responseFile = $responsesDir . 'flickr-photosets-getinfo.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mockResponse->shouldReceive('getBody')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

    public function newPhoto(\RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = parent::newPhoto();
        $mock->shouldReceive('all')
            ->with('72157633782247768')
            ->once()
            ->andReturn(['photo_array']);

        return $mock;

    }

}

class FlickrGalleryFindNotFoundStub extends FlickrGalleryStub
{
    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
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

        $responseFile = $responsesDir . 'flickr-photosets-getinfo-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mockResponse->shouldReceive('getBody')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}
