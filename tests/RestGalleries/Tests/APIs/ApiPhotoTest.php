<?php namespace RestGalleries\Tests\APIs;

use Mockery;
use RestGalleries\Tests\APIs\StubService\Photo;

class ApiPhotoTest extends \RestGalleries\Tests\TestCase
{
    public function testNewRequestReturnsCorrectObject()
    {
        $model   = new Photo;
        $request = $model->newRequest();

        assertThat($request, is(anInstanceOf('RestGalleries\\Http\\RequestAdapter')));

    }

    public function testAddPlugin()
    {
        $model      = new PhotoAddPluginStub;
        $pluginMock = Mockery::mock('RestGalleries\\Http\\Plugins\\RequestPluginAdapter');

        $model->addPlugin($pluginMock);
        $model->addPlugin($pluginMock);
        $model->newRequest();

    }

    public function testAllReturnsCorrectObject()
    {
        $model  = new PhotoAllStub;
        $photos = $model->all('some-fake-gallery-id');

        assertThat($photos, is(anInstanceOf('Illuminate\Support\Collection')));

    }

    public function testAllEmptyReturn()
    {
        $model  = new PhotoAllEmptyReturnStub;
        $photos = $model->all('invalid-gallery-id');

        assertThat($photos, is(nullValue()));

    }

    public function testFindReturnsCorrectObject()
    {
        $model = new PhotoFindStub;
        $photo = $model->find('some-fake-photo-id');

        assertThat($photo, is(anInstanceOf('Illuminate\Support\Fluent')));
    }

    public function testFindReturnedObject()
    {
        $model = new PhotoFindStub;
        $photo = $model->find('some-fake-photo-id');

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
        $model = new PhotoFindNotFoundStub;
        $photo = $model->find('invalid-photo-id');

        assertThat($photo, is(nullValue()));

    }

}

class PhotoStub extends Photo
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

}


class PhotoAddPluginStub extends Photo
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

class PhotoAllStub extends PhotoStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/photo/';
        $mock         = parent::newRequest();

        if (get_caller_function() == 'fetchIds') {
            $responseFile = $responsesDir . 'mockservice-rest-photos.json';
            $responseBody = json_decode(file_get_contents($responseFile), true);

            $mock->shouldReceive('GET')
                ->with('gallery/some-fake-gallery-id/photos')
                ->once()
                ->andReturn(Mockery::self())
                ->shouldReceive('getBody')
                ->with('array')
                ->once()
                ->andReturn($responseBody);

        }

        if (get_caller_function() == 'fetchPhoto') {
            $responseFile = $responsesDir . 'mockservice-rest-photo.json';
            $responseBody = json_decode(file_get_contents($responseFile));

            $mock->shouldReceive('GET')
                ->with('photo/some-fake-photo-id')
                ->atMost()
                ->once()
                ->andReturn($mock);

            $mock->shouldReceive('GET')
                ->with('photo/some-fake-photo-id-2')
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

class PhotoAllEmptyReturnStub extends PhotoStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/photo/';
        $responseFile = $responsesDir . 'mockservice-rest-photos-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile), true);

        $mock = parent::newRequest();
        $mock->shouldReceive('GET')
            ->with('gallery/invalid-gallery-id/photos')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('getBody')
            ->with('array')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class PhotoFindStub extends PhotoStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/photo/';
        $responseFile = $responsesDir . 'mockservice-rest-photo.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newRequest();
        $mock->shouldReceive('GET')
            ->with('photo/some-fake-photo-id')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }
}

class PhotoFindNotFoundStub extends PhotoStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/photo/';
        $responseFile = $responsesDir . 'mockservice-rest-photo-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newRequest();
        $mock->shouldReceive('GET')
            ->with('photo/invalid-photo-id')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}
