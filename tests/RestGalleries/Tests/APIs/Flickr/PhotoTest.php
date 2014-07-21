<?php namespace RestGalleries\Tests\APIs;

use Mockery;

class PhotoTest extends \RestGalleries\Tests\TestCase
{
    public function testAllReturnsCorrectObject()
    {
        $model  = new FlickrPhotoAllStub;
        $photos = $model->all('72157633782247768');

        assertThat($photos, is(anInstanceOf('Illuminate\Support\Collection')));

    }

    public function testAllEmptyReturn()
    {
        $model  = new FlickrPhotoAllEmptyReturnStub;
        $photos = $model->all('invalid-gallery-id');

        assertThat($photos, is(nullValue()));

    }

    public function testFindReturnsCorrectObject()
    {
        $model = new FlickrPhotoFindStub;
        $photo = $model->find('8876434399');

        assertThat($photo, is(anInstanceOf('Illuminate\Support\Fluent')));

    }

    public function testFindReturnedObject()
    {
        $model = new FlickrPhotoFindStub;
        $photo = $model->find('8876434399');

        assertThat($photo, set('id'));
        assertThat($photo, set('title'));
        assertThat($photo, set('description'));
        assertThat($photo, set('url'));
        assertThat($photo, set('created'));
        assertThat($photo, set('views'));
        assertThat($photo, set('source'));
        assertThat($photo, set('source_thumbnail'));

    }

    public function testFindNotFound()
    {
        $model = new FlickrPhotoFindNotFoundStub;
        $photo = $model->find('some-invalid-photo-id');

        assertThat($photo, is(nullValue()));

    }

}

class FlickrPhotoStub extends \RestGalleries\APIs\Flickr\Photo
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleRequest');
        $mock->shouldReceive('init')
            ->with('http://api.flickr.com/services/rest/')
            ->atMost()
            ->times(5)
            ->andReturn(Mockery::self());

        return parent::newRequest($mock);

    }

}

class FlickrPhotoAllStub extends FlickrPhotoStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/responses/photo/';
        $mock         = parent::newRequest();

        if (get_caller_function() == 'fetchIds') {
            $responseFile = $responsesDir . 'flickr-photosets-getphotos.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $query = [
                'format'         => 'json',
                'nojsoncallback' => 1,
                'method'         => 'flickr.photosets.getPhotos',
                'photoset_id'    => '72157633782247768',
                'extras'         => '',
                'privacy_filter' => 1,
                'per_page'       => 50,
                'page'           => 1,
                'media'          => 'photos'
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

        if (get_caller_function() == 'fetchPhoto') {
            $responseFile = $responsesDir . 'flickr-photos-getinfo.json';
            $responseBody = json_decode(file_get_contents($responseFile));

            $query = $query2 = [
                'format'         => 'json',
                'nojsoncallback' => 1,
                'method'         => 'flickr.photos.getInfo',
                'photo_id'       => '8876434399',
                'secret'         => ''
            ];
            $query2['photo_id'] = '8877049006';

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

        if (get_caller_function() == 'fetchImages') {
            $responseFile = $responsesDir . 'flickr-photos-getsizes.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $query = [
                'format'         => 'json',
                'nojsoncallback' => 1,
                'method'         => 'flickr.photos.getSizes',
                'photo_id'       => '8876434399'
            ];

            $mock->shouldReceive('setQuery')
                ->with($query)
                ->once()
                ->andReturn($mock)
                ->shouldReceive('GET')
                ->once()
                ->andReturn($mock)
                ->shouldReceive('getBody')
                ->with('array')
                ->once()
                ->andReturn($responseBody);

        }

        return $mock;

    }

}

class FlickrPhotoAllEmptyReturnStub extends FlickrPhotoStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/responses/photo/';
        $responseFile = $responsesDir . 'flickr-photosets-getphotos-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile), true);

        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photosets.getPhotos',
            'photoset_id'    => 'invalid-gallery-id',
            'extras'         => '',
            'privacy_filter' => 1,
            'per_page'       => 50,
            'page'           => 1,
            'media'          => 'photos'
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

}

class FlickrPhotoFindStub extends FlickrPhotoStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/responses/photo/';
        $mock         = parent::newRequest();

        if (get_caller_function() == 'fetchPhoto') {
            $responseFile = $responsesDir . 'flickr-photos-getinfo.json';
            $responseBody = json_decode(file_get_contents($responseFile));

            $query = [
                'format'         => 'json',
                'nojsoncallback' => 1,
                'method'         => 'flickr.photos.getInfo',
                'photo_id'       => '8876434399',
                'secret'         => '',
            ];

            $mock->shouldReceive('setQuery')
                ->with($query)
                ->once()
                ->andReturn(Mockery::self())
                ->shouldReceive('GET')
                ->once()
                ->andReturn(Mockery::self())
                ->shouldReceive('getBody')
                ->once()
                ->andReturn($responseBody);

        }

        if (get_caller_function() == 'fetchImages') {
            $responseFile = $responsesDir . 'flickr-photos-getsizes.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $query = [
                'format'         => 'json',
                'nojsoncallback' => 1,
                'method'         => 'flickr.photos.getSizes',
                'photo_id'       => '8876434399'
            ];

            $mock->shouldReceive('setQuery')
                ->with($query)
                ->once()
                ->andReturn($mock)
                ->shouldReceive('GET')
                ->once()
                ->andReturn($mock)
                ->shouldReceive('getBody')
                ->with('array')
                ->once()
                ->andReturn($responseBody);

        }

        return $mock;

    }

}

class FlickrPhotoFindNotFoundStub extends FlickrPhotoStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/responses/photo/';
        $responseFile = $responsesDir . 'flickr-photos-getinfo-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photos.getInfo',
            'photo_id'       => 'some-invalid-photo-id',
            'secret'         => ''
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
