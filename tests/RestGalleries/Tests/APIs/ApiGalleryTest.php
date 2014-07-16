<?php namespace RestGalleries\Tests\APIs;

use Mockery;
use RestGalleries\Tests\APIs\StubService\Gallery;

class ApiGalleryTest extends \RestGalleries\Tests\TestCase
{
    public function testNewRequestReturnsCorrectObject()
    {
        $model   = new Gallery;
        $request = $model->newRequest();

        assertThat($request, is(anInstanceOf('RestGalleries\\Http\\RequestAdapter')));

    }

    public function testNewPhotoReturnsCorrectObject()
    {
        $model = new Gallery;
        $photo = $model->newPhoto();

        assertThat($photo, is(anInstanceOf('RestGalleries\\Tests\\APIs\\StubService\\Photo')));

    }

    public function testAddAuthenticationCallsAuthPlugin()
    {
        $model = new GalleryAddAuthenticationStub;
        $model->addAuthentication(['dummy-credentials']);
    }

    public function testAddCacheCallsCachePlugin()
    {
        $model = new GalleryAddCacheCallsCachePluginStub;
        $model->addCache('fake-cache-system', ['dummy-path']);
    }

    public function testAllReturnsCorrectObject()
    {
        $model = new GalleryAllStub;
        $galleries = $model->all();

        assertThat($galleries, is(anInstanceOf('Illuminate\Support\Collection')));

    }

    public function testFindReturnsCorrectObject()
    {

    }

}

class GalleryAddAuthenticationStub extends Gallery
{
    protected function newRequestAuthPlugin()
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\Plugins\\GuzzleAuth');
        $mock->shouldReceive('add')
            ->with(['dummy-credentials'])
            ->once();

        return $mock;

    }

}

class GalleryAddCacheCallsCachePluginStub extends Gallery
{
    protected function newRequestCachePlugin()
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\Plugins\\GuzzleCache');
        $mock->shouldReceive('add')
            ->with('fake-cache-system', ['dummy-path'])
            ->once();

        return $mock;

    }
}

class GalleryStub extends Gallery
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleRequest');
        $mock->shouldReceive('init')
            ->with('http://www.mockservice.com/rest/')
            ->atMost()
            ->times(3)
            ->andReturn($mock);

        return parent::newRequest($mock);

    }

    public function newPhoto(\RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = Mockery::mock('RestGalleries\\Tests\\APIs\\StubService\\Photo');
        $mock->shouldReceive('all')
            ->with('some-fake-gallery-id')
            ->once()
            ->andReturn(new \Illuminate\Support\Collection);

        return parent::newPhoto($mock);

    }

}

class GalleryAllStub extends GalleryStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $mock = parent::newRequest();
        $responsesDir = __DIR__ . '/StubService/responses/gallery/';

        if (get_caller_function() == 'fetchIds') {
            $responseFile = $responsesDir . 'mockservice-rest-galleries.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $mock->shouldReceive('GET')
                ->with('galleries')
                ->once()
                ->andReturn($mock);

            $mock->shouldReceive('getBody')
                ->with('array')
                ->once()
                ->andReturn($responseBody);

        }

        if (get_caller_function() == 'fetchGallery') {
            $responseFile = $responsesDir . 'mockservice-rest-gallery.json';
            $responseBody = json_decode(file_get_contents($responseFile));

            $mock->shouldReceive('GET')
                ->with('gallery/some-fake-gallery-id')
                ->atMost()
                ->once()
                ->andReturn($mock);

            $mock->shouldReceive('GET')
                ->with('gallery/some-fake-gallery-id-2')
                ->atMost()
                ->once()
                ->andReturn($mock);

            $mock->shouldReceive('getBody')
                ->once()
                ->andReturn($responseBody);


        }

        return $mock;

    }

}


class GalleryFindStub extends GalleryStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $mock = parent::newRequest();
        $responsesDir = __DIR__ . '/StubService/responses/gallery/';
        $responseFile = $responsesDir . 'mockservice-rest-gallery.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock->shouldReceive('GET')
            ->with('gallery/some-fake-gallery-id')
            ->once()
            ->andReturn($mock);

        $mock->shouldReceive('getBody')
            ->once()
            ->andReturn($responseBody);

    }

}
