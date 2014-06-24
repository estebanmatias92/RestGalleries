<?php



class RestGalleryTest extends TestCase
{
    public function testCredentialsManipulation()
    {
        $model = new RestGalleryModelStub;

        $model->setCredentials([
            'consumer_key' => 'test-consumer-key',
            'token'        => 'test-token',
            'token_secret' => 'test-token-secret',
            'custom_key'   => 'test-custom-key',
        ]);

        $expectedCredentials = [
            'consumer_key'    => 'test-consumer-key',
            'consumer_secret' => 'stub-consumer-secret',
            'token'           => 'test-token',
            'token_secret'    => 'test-token-secret',
            'custom_key'      => 'test-custom-key'
        ];

        assertThat($model->getCredentials(), is(equalTo($expectedCredentials)));

    }

    public function testServiceNameManipulation()
    {
        $model   = new RestGalleryModelStub;
        $service = $model->getService();

        $model->setService('test-service-name');
        $serviceModified = $model->getService();

        assertThat($service, is(equalTo('stub')));
        assertThat($serviceModified, is(equalTo('test-service-name')));

    }

    public function testAuthenticate()
    {
        $result = RestGalleryAuthenticateStub::authenticate(['token-credentials']);
    }

    public function testAll()
    {
        $galleries = RestGalleryAllStub::authenticate(['token-credentials'])->all();

        assertThat($galleries, is(equalTo('foo')));

    }

    public function testFind()
    {
        $gallery = RestGalleryfindStub::authenticate(['token-credentials'])->find('1');

        assertThat($gallery, is(equalTo('foo')));

    }


    public function testConnect()
    {
        $user = RestGalleryConnectStub::connect(['client-credentials']);

        assertThat($user, is(equalTo('foo')));

    }

    public function testVerifyCredentials()
    {
        $user = RestGalleryVerifyCredentialsStub::verifyCredentials(['token-credentials']);

        assertThat($user, is(equalTo('foo')));

    }

}

class RestGalleryModelStub extends RestGalleries\RestGallery
{
    protected $service = 'stub';

    protected $credentials = [
        'consumer_key'    => 'stub-consumer-key',
        'consumer_secret' => 'stub-consumer-secret',
    ];

}

class RestGalleryAuthenticateStub extends RestGalleries\RestGallery
{
    public function newQuery()
    {
        $mock = Mockery::mock('RestGalleries\\APIs\\Flickr\\Gallery');
        $mock->shouldReceive('setAuth')
            ->with(['token-credentials'])
            ->once();

        return $mock;

    }

}

class RestGalleryAllStub extends RestGalleries\RestGallery
{
    public function newQuery()
    {
        $mock = Mockery::mock('RestGalleries\\APIs\\Flickr\\Gallery');
        $mock->shouldReceive('setAuth')
            ->with(['token-credentials'])
            ->once();

        $mock->shouldReceive('all')
            ->with()
            ->once()
            ->andReturn('foo');

        return $mock;

    }

}

class RestGalleryFindStub extends RestGalleries\Restgallery
{
    public function newQuery()
    {
        $mock = Mockery::mock('RestGalleries\\APIs\\Flickr\\Gallery');
        $mock->shouldReceive('setAuth')
            ->with(['token-credentials'])
            ->once();

        $mock->shouldReceive('find')
            ->with('1')
            ->once()
            ->andReturn('foo');

        return $mock;

    }

}

class RestGalleryConnectStub extends RestGalleries\RestGallery
{
    public function newAuthentication()
    {
        $mock = Mockery::mock('RestGalleries\\APIs\\Flickr\\User');
        $mock->shouldReceive('connect')
            ->with(['client-credentials'])
            ->once()
            ->andReturn('foo');

        return $mock;

    }

}

class RestGalleryVerifyCredentialsStub extends RestGalleries\RestGallery
{
    public function newAuthentication()
    {
        $mock = Mockery::mock('RestGalleries\\APIs\\Flickr\\User');
        $mock->shouldReceive('verifyCredentials')
            ->with(['token-credentials'])
            ->once()
            ->andReturn('foo');

        return $mock;

    }

}
