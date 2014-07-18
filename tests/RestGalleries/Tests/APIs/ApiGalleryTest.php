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
        $model     = new GalleryAllStub;
        $galleries = $model->all();

        assertThat($galleries, is(anInstanceOf('Illuminate\Support\Collection')));

    }

    public function testAllEmptyReturn()
    {
        $model     = new GalleryAllEmptyReturnStub;
        $galleries = $model->all();

        assertThat($galleries, is(nullValue()));

    }

    public function testFindReturnsCorrectObject()
    {
        $model   = new GalleryFindStub;
        $gallery = $model->find('some-fake-gallery-id');

        assertThat($gallery, is(anInstanceOf('Illuminate\Support\Fluent')));
    }

    public function testFindReturnedObject()
    {
        $model   = new GalleryFindStub;
        $gallery = $model->find('some-fake-gallery-id');

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
        $model   = new GalleryFindNotFoundStub;
        $gallery = $model->find('invalid-gallery-id');

        assertThat($gallery, is(nullValue()));
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
            ->andReturn(Mockery::self());

        return parent::newRequest($mock);

    }

    public function newPhoto(\RestGalleries\Interfaces\PhotoAdapter $photo = null)
    {
        $mock = Mockery::mock('RestGalleries\\Interfaces\\PhotoAdapter');
        $mock->shouldReceive('all')
            ->with('some-fake-gallery-id')
            ->once()
            ->andReturn(['photo_array']);

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
            ->andReturn(Mockery::self())
            ->shouldReceive('addPlugin')
            ->times(2);

        return parent::newRequest($mock);

    }

}

class GalleryAllStub extends GalleryStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/gallery/';
        $mock         = parent::newRequest();

        if (get_caller_function() == 'fetchIds') {
            $responseFile = $responsesDir . 'mockservice-rest-galleries.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $mock->shouldReceive('GET')
                ->with('galleries')
                ->once()
                ->andReturn(Mockery::self())
                ->shouldReceive('getBody')
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

class GalleryAllEmptyReturnStub extends GalleryStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/gallery/';
        $responseFile = $responsesDir . 'mockservice-rest-galleries-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile), true);

        $mock = parent::newRequest();
        $mock->shouldReceive('GET')
            ->with('galleries')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('getBody')
            ->with('array')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class GalleryFindStub extends GalleryStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/gallery/';
        $responseFile = $responsesDir . 'mockservice-rest-gallery.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newRequest();
        $mock->shouldReceive('GET')
            ->with('gallery/some-fake-gallery-id')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class GalleryFindNotFoundStub extends GalleryStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/gallery/';
        $responseFile = $responsesDir . 'mockservice-rest-gallery-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newRequest();
        $mock->shouldReceive('GET')
            ->with('gallery/invalid-gallery-id')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}
