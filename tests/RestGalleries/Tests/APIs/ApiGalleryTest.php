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

        assertThat($photo, is(anInstanceOf('RestGalleries\\Interfaces\\PhotoAdapter')));
        assertThat($photo, is(anInstanceOf('RestGalleries\\Tests\\APIs\\StubService\\Photo')));

    }

    public function testAddPlugin()
    {
        $model      = new GalleryAddPluginStub;
        $pluginMock = Mockery::mock('RestGalleries\\Http\\Plugins\\RequestPluginAdapter');

        $model->addPlugin($pluginMock);
        $model->addPlugin($pluginMock);
        $model->newRequest();

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

class GalleryStub extends Gallery
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\RequestAdapter');
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

class GalleryAddPluginStub extends Gallery
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\RequestAdapter');
        $mock->shouldReceive('init')
            ->with('http://www.mockservice.com/rest/')
            ->once()
            ->andReturn($mock);

        $mock->shouldReceive('addPlugin')
            ->times(2);

        return parent::newRequest($mock);

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
