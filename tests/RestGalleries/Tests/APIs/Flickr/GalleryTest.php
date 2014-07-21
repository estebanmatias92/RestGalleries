<?php namespace RestGalleries\Tests\APIs\Flickr;

use Mockery;

class GalleryTest extends \RestGalleries\Tests\TestCase
{
    public function testAllReturnsCorrectObject()
    {
        $model     = new FlickrGalleryAllStub;
        $galleries = $model->all();

        assertThat($galleries, is(anInstanceOf('Illuminate\Support\Collection')));

    }

    public function testAllEmptyReturn()
    {
        $model     = new FlickrGalleryAllEmptyReturnStub;
        $galleries = $model->all();

        assertThat($galleries, is(nullValue()));

    }

    public function testFindReturnsCorrectObject()
    {
        $model   = new FlickrGalleryFindStub;
        $gallery = $model->find('72157633782247768');

        assertThat($gallery, is(anInstanceOf('Illuminate\Support\Fluent')));

    }

    public function testFindReturnedObject()
    {
        $model   = new FlickrGalleryFindStub;
        $gallery = $model->find('72157633782247768');

        assertThat($gallery, set('id'));
        assertThat($gallery, set('title'));
        assertThat($gallery, set('description'));
        assertThat($gallery, set('photos'));
        assertThat($gallery, set('created'));
        assertThat($gallery, set('url'));
        assertThat($gallery, set('size'));
        assertThat($gallery, set('user_id'));
        assertThat($gallery, set('thumbnail'));
        assertThat($gallery, set('views'));

    }

    public function testFindNotFound()
    {
        $model   = new FlickrGalleryFindNotFoundStub;
        $gallery = $model->find('some-invalid-gallery-id');

        assertThat($gallery, is(nullValue()));

    }

}

class FlickrGalleryStub extends \RestGalleries\APIs\Flickr\Gallery
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $http = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\GuzzleRequest');
        $mock->shouldReceive('init')
            ->with('http://api.flickr.com/services/rest/')
            ->atMost()
            ->times(3)
            ->andReturn(Mockery::self());

        return parent::newRequest($mock);

    }

    public function newPhoto(\RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = Mockery::mock('RestGalleries\\Interfaces\\PhotoAdapter');

        return $mock;

    }

}

class FlickrGalleryAllStub extends FlickrGalleryStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/gallery/';
        $mock         = parent::newRequest();

        if (get_caller_function() == 'fetchIds') {
            $responseFile = $responsesDir . 'flickr-photosets-getlist.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $query = [
                'format'               => 'json',
                'nojsoncallback'       => 1,
                'method'               => 'flickr.photosets.getList',
                'page'                 => 1,
                'per_page'             => 50,
                'primary_photo_extras' => ''
            ];

            $mock->shouldReceive('setQuery')
                ->with($query)
                ->once()
                ->andReturn(Mockery::self())
                ->shouldReceive('GET')
                ->once()
                ->andReturn(Mockery::self())
                ->shouldReceive('getBody')
                ->with('array')
                ->once()
                ->andReturn($responseBody);

        }

        if (get_caller_function() == 'fetchGallery') {
            $responseFile = $responsesDir . 'flickr-photosets-getinfo.json';
            $responseBody = json_decode(file_get_contents($responseFile));

            $query = $query2 = [
                'format'         => 'json',
                'nojsoncallback' => 1,
                'method'         => 'flickr.photosets.getInfo',
                'photoset_id'    => '72157633782247768'
            ];
            $query2['photoset_id'] = '72157633780835561';

            $mock->shouldReceive('setQuery')
                ->with($query)
                ->atMost()
                ->once()
                ->andReturn($mock);

            $mock->shouldReceive('setQuery')
                ->with($query2)
                ->atMost()
                ->once()
                ->andReturn($mock);

            $mock->shouldReceive('GET')
                ->once()
                ->andReturn($mock)
                ->shouldReceive('getBody')
                ->once()
                ->andReturn($responseBody);

        }

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
    public function newRequest(\RestGalleries\Http\RequestAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/gallery/';
        $responseFile = $responsesDir . 'flickr-photosets-getlist-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile), true);

        $query = [
            'format'               => 'json',
            'nojsoncallback'       => 1,
            'method'               => 'flickr.photosets.getList',
            'page'                 => 1,
            'per_page'             => 50,
            'primary_photo_extras' => ''
        ];

        $mock = parent::newRequest();
        $mock->shouldReceive('setQuery')
            ->with($query)
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('GET->getBody')
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
    public function newRequest(\RestGalleries\Http\RequestAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/gallery/';
        $responseFile = $responsesDir . 'flickr-photosets-getinfo.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photosets.getInfo',
            'photoset_id'    => '72157633782247768'
        ];

        $mock = parent::newRequest();
        $mock->shouldReceive('setQuery')
            ->with($query)
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('GET->getBody')
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
    public function newRequest(\RestGalleries\Http\RequestAdapter $http = null)
    {
        $responsesDir = __DIR__ . '/responses/gallery/';
        $responseFile = $responsesDir . 'flickr-photosets-getinfo-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photosets.getInfo',
            'photoset_id'    => 'some-invalid-gallery-id'
        ];

        $mock = parent::newRequest();
        $mock->shouldReceive('setQuery')
            ->with($query)
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('GET->getBody')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}
