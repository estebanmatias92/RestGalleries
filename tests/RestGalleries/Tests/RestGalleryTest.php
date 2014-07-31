<?php namespace RestGalleries\Tests;

use Mockery;

class RestGalleryTest extends \RestGalleries\Tests\TestCase
{
    public function testSetService()
    {
        $model          = new RestGalleryStub;
        $defaultService = $model->getService();

        $model->setService('foo');
        $newService = $model->getService();

        assertThat($defaultService, is(equalTo('StubService')));
        assertThat($newService, is(equalTo('foo')));

    }

    public function testSetAuth()
    {
        $tokenCredentials = [
            'access_token' => 'dummy-access-token',
            'expires'      => 'dummy-expires-date'
        ];

        $model   = new RestGalleryStub;
        $plugins = $model->setAuth($tokenCredentials)
            ->getPlugins();

        assertThat($plugins, hasKey('auth'));
        assertThat($plugins['auth'], is(anInstanceOf('RestGalleries\Http\Plugins\RequestPluginAdapter')));

    }

    public function testSetAuthFails()
    {
        $this->setExpectedException('InvalidArgumentException', 'Credentials keys are invalid.');

        $model = new RestGalleryStub;
        $model->setAuth(['invalid-token-credentials']);

    }

    public function testSetCache()
    {
        $model   = new RestGalleryStub;
        $plugins = $model->setCache('file')
            ->getPlugins();

        assertThat($plugins, hasKey('cache'));
        assertThat($plugins['cache'], is(anInstanceOf('RestGalleries\Http\Plugins\RequestPluginAdapter')));

    }

    public function testSetCacheFails()
    {
        $this->setExpectedException('InvalidArgumentException', 'Cache system is invalid.');

        $model = new RestGalleryStub;
        $model->setCache(['invalid-cache-sytem']);

    }

    public function testNewGallery()
    {
        $model        = new RestGalleryStub;
        $modelGallery = $model->newGallery();

        assertThat($modelGallery, is(anInstanceOf('RestGalleries\Interfaces\GalleryAdapter')));

    }

    public function testNewGalleryAddPlugin()
    {
        $model = new RestGalleryNewGalleryAddPluginStub;
        $model->setAuth(['valid-token-credentials'])
            ->setCache('valid-cache-system')
            ->newGallery();

    }

    public function testNewUser()
    {
        $model     = new RestGalleryStub;
        $modelUser = $model->newUser();

        assertThat($modelUser, is(anInstanceOf('RestGalleries\Interfaces\UserAdapter')));
    }

    public function testAll()
    {
        $model     = new RestGalleryAllStub;
        $galleries = $model->all();

        assertThat($galleries, is(equalTo('foo')));

    }

    public function testAllReturnsEmpty()
    {
        $model     = new RestGalleryAllReturnsEmptyStub;
        $galleries = $model->all();

        assertThat($galleries, is(nullValue()));

    }

    public function testFind()
    {
        $model     = new RestGalleryFindStub;
        $galleries = $model->find('valid-gallery-id');

        assertThat($galleries, is(equalTo('foo')));

    }

    public function testFindNotFound()
    {
        $model     = new RestGalleryFindNotFoundStub;
        $galleries = $model->find('invalid-gallery-id');

        assertThat($galleries, is(nullValue()));

    }

    public function testConnect()
    {
        $user = RestGalleryConnectStub::connect();

        assertThat($user, is(equalTo('foo')));

    }

    public function testVerifyCredentials()
    {
        $user = RestGalleryVerifyCredentialsStub::verifyCredentials(['valid-token-credentials']);

        assertThat($user, is(equalTo('foo')));

    }

}


class RestGalleryStub extends \RestGalleries\RestGallery
{
    protected $service = 'StubService';
    protected $clientCredentials = ['valid-client-credentials'];
}

class RestGalleryNewGalleryAddPluginStub extends RestGalleryStub
{
    public function newGallery(\RestGalleries\Interfaces\GalleryAdapter $gallery = null)
    {
        $mock = Mockery::mock('RestGalleries\\Tests\\APIs\\StubService\\Gallery');
        $mock->shouldReceive('addPlugin')
            ->times(2);

        return parent::newGallery($mock);

    }

    public function setAuth(array $tokenCredentials)
    {
        $this->plugins['auth'] = Mockery::mock('RestGalleries\\Http\\Plugins\\RequestPluginAdapter');
        return $this;
    }

    public function setCache($system, array $path = array())
    {
        $this->plugins['cache'] = Mockery::mock('RestGalleries\\Http\\Plugins\\RequestPluginAdapter');
        return $this;
    }

}

class RestGalleryAllStub extends RestGalleryStub
{
    public function newGallery(\RestGalleries\Interfaces\GalleryAdapter $gallery = null)
    {
        $mock = Mockery::mock('RestGalleries\\Tests\\APIs\\StubService\\Gallery');
        $mock->shouldReceive('all')
            ->once()
            ->andReturn('foo');

        return parent::newGallery($mock);

    }

}

class RestGalleryAllReturnsEmptyStub extends RestGalleryStub
{
    public function newGallery(\RestGalleries\Interfaces\GalleryAdapter $gallery = null)
    {
        $mock = Mockery::mock('RestGalleries\\Tests\\APIs\\StubService\\Gallery');
        $mock->shouldReceive('all')
            ->once()
            ->andReturn(null);

        return parent::newGallery($mock);

    }

}

class RestGalleryFindStub extends RestGalleryStub
{
    public function newGallery(\RestGalleries\Interfaces\GalleryAdapter $gallery = null)
    {
        $mock = Mockery::mock('RestGalleries\\Tests\\APIs\\StubService\\Gallery');
        $mock->shouldReceive('find')
            ->with('valid-gallery-id')
            ->once()
            ->andReturn('foo');

        return parent::newGallery($mock);

    }

}

class RestGalleryFindNotFoundStub extends RestGalleryStub
{
    public function newGallery(\RestGalleries\Interfaces\GalleryAdapter $gallery = null)
    {
        $mock = Mockery::mock('RestGalleries\\Tests\\APIs\\StubService\\Gallery');
        $mock->shouldReceive('find')
            ->with('invalid-gallery-id')
            ->once()
            ->andReturn(null);

        return parent::newGallery($mock);

    }

}


class RestGalleryConnectStub extends RestGalleryStub
{
    public function newUser()
    {
        $mock = Mockery::mock('RestGalleries\\Tests\\APIs\\StubService\\User');
        $mock->shouldReceive('connect')
            ->with(['valid-client-credentials'])
            ->once()
            ->andReturn('foo');

        return $mock;

    }

}

class RestGalleryVerifyCredentialsStub extends RestGalleryStub
{
    public function newUser()
    {
        $mock = Mockery::mock('RestGalleries\\Tests\\APIs\\StubService\\User');
        $mock->shouldReceive('verifyCredentials')
            ->with(['valid-token-credentials'])
            ->once()
            ->andReturn('foo');

        return $mock;

    }

}

