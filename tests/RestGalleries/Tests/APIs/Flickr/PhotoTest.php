<?php namespace RestGalleries\Tests\APIs;

use Mockery;

class PhotoTest extends \RestGalleries\Tests\TestCase
{
    public function testAll()
    {
        $model = new FlickrPhotoAllStub;

        $galleries = $model->all('72157633782247768');

        assertThat($galleries, is(objectValue()));

    }

    public function testAllEmptyReturn()
    {
        $model = new FlickrPhotoAllEmptyReturnStub;

        $galleries = $model->all('some-invalid-gallery-id');

        assertThat($galleries, is(nullValue()));
    }

    public function testFind()
    {
        $model = new FlickrPhotoFindStub;

        $gallery = $model->find('8876434399');

        assertThat($gallery, is(objectValue()));

    }

    public function testFindNotFound()
    {
        $model = new FlickrPhotoFindNotFoundStub;

        $gallery = $model->find('some-invalid-id');

        assertThat($gallery, is(nullValue()));

    }

}

class FlickrPhotoStub extends \RestGalleries\APIs\Flickr\Photo
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
            ->times(5)
            ->andReturn($mock);

        $mock->shouldReceive('setAuth')
            ->with(['token_credentials'])
            ->atMost()
            ->times(5);

        $mock->shouldReceive('setCache')
            ->with('cache-name', ['cache_path'])
            ->atMost()
            ->times(5);

        return parent::newHttp($mock);

    }

}

class FlickrPhotoAllStub extends FlickrPhotoStub
{
    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/photo/';
        $mock         = parent::newHttp();
        $mockResponse = Mockery::mock('RestGalleries\\Http\\Response');

        $mock->shouldReceive('setQuery')
            ->with(typeOf('array'))
            ->once();

        $mock->shouldReceive('GET')
            ->once()
            ->andReturn($mockResponse);

        if (get_caller_function() == 'fetchIds') {
            $responseFile = $responsesDir . 'flickr-photosets-getphotos.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $mockResponse->shouldReceive('getBody')
                ->with('array')
                ->once()
                ->andReturn($responseBody);
        }

        if (get_caller_function() == 'fetchPhoto') {
            $responseFile = $responsesDir . 'flickr-photos-getinfo.json';
            $responseBody = json_decode(file_get_contents($responseFile));

            $mockResponse->shouldReceive('getBody')
                ->once()
                ->andReturn($responseBody);
        }

        if (get_caller_function() == 'fetchImages') {
            $responseFile = $responsesDir . 'flickr-photos-getsizes.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $mockResponse->shouldReceive('getBody')
                ->with('array')
                ->once()
                ->andReturn($responseBody);
        }

        return $mock;

    }

}

class FlickrPhotoAllEmptyReturnStub extends FlickrPhotoStub
{
    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/photo/';
        $mock         = parent::newHttp();
        $mockResponse = Mockery::mock('RestGalleries\\Http\\Response')->makePartial();

        $mock->shouldReceive('setQuery')
            ->with(typeOf('array'))
            ->once();

        $mock->shouldReceive('GET')
            ->once()
            ->andReturn($mockResponse);

        $responseFile = $responsesDir . 'flickr-photosets-getphotos-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile), true);

        $mockResponse->shouldReceive('getBody')
            ->with('array')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class FlickrPhotoFindStub extends FlickrPhotoStub
{
    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/photo/';
        $mock         = parent::newHttp();
        $mockResponse = Mockery::mock('RestGalleries\\Http\\Response');

        $mock->shouldReceive('setQuery')
            ->with(typeOf('array'))
            ->once();

        $mock->shouldReceive('GET')
            ->once()
            ->andReturn($mockResponse);

        if (get_caller_function() == 'fetchPhoto') {
            $responseFile = $responsesDir . 'flickr-photos-getinfo.json';
            $responseBody = json_decode(file_get_contents($responseFile));

            $mockResponse->shouldReceive('getBody')
                ->once()
                ->andReturn($responseBody);
        }

        if (get_caller_function() == 'fetchImages') {
            $responseFile = $responsesDir . 'flickr-photos-getsizes.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $mockResponse->shouldReceive('getBody')
                ->with('array')
                ->once()
                ->andReturn($responseBody);
        }

        return $mock;

    }

}

class FlickrPhotoFindNotFoundStub extends FlickrPhotoStub
{
    public function newHttp(\RestGalleries\Http\HttpAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/photo/';
        $mock         = parent::newHttp();
        $mockResponse = Mockery::mock('RestGalleries\\Http\\Response');

        $mock->shouldReceive('setQuery')
            ->with(typeOf('array'))
            ->once();

        $mock->shouldReceive('GET')
            ->once()
            ->andReturn($mockResponse);

        $responseFile = $responsesDir . 'flickr-photos-getinfo-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mockResponse->shouldReceive('getBody')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}
