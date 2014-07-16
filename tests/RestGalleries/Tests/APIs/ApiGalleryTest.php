<?php namespace RestGalleries\Tests\APIs;

use Mockery;
use RestGalleries\Tests\APIs\StubService\Gallery;

class ApiGalleryTest extends \RestGalleries\Tests\TestCase
{
    public function testNewRequestReturnsCorrectObject()
    {
        $model   = new Gallery;
        $request = $model->newRequest();

        assertThat($request, is(anInstanceOf('RestGalleries\\Http\\Guzzle\\GuzzleRequest')));

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
        $model = new GalleryAddCacheStub;
        $model->addCache('fake-cache-system', ['dummy-path']);
    }

    public function testAllReturnsCorrectObject()
    {

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

class GalleryAddCacheStub extends Gallery
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
