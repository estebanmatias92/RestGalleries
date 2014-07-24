<?php namespace RestGalleries\Tests\Auth\OhmyAuth;

use Mockery;
use RestGalleries\Auth\OhmyAuth\OhmyAuth;

class OhmyAuthTest extends \RestGalleries\Tests\TestCase
{
    public function testConnectObjectReturned()
    {
        $clientCredentials = [
            'client_id'     => 'dummy-client-id',
            'client_secret' => 'dummy-client-secret',
            'redirect'      => 'http://www.mywebapp.com/galleries'
        ];

        $endPoints = [
            'authorize' => 'http://www.mockservice.com/auth/authorize',
            'access'    => 'http://www.mockservice.com/auth/access'
        ];

        $userData = OhmyAuthConnectStub::connect($clientCredentials, $endPoints, 'http://www.mockservice.com/rest/user');

        assertThat($userData->tokens, set('access_token'));
        assertThat($userData->tokens, set('expires'));

    }

}

class OhmyAuthStub extends OhmyAuth
{
    public function __construct()
    {
        $this->auth = Mockery::mock('ohmy\\Auth');
    }

    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleRequest');

        return parent::newRequest($mock);

    }

}

class OhmyAuthConnectStub extends OhmyAuthStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responseData = new \stdClass;
        $responseData->whateverUserData = 'some-fake-user-data';

        $mock = parent::newRequest();
        $mock->shouldReceive('init')
            ->with('http://www.mockservice.com/rest/user')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('addPlugins')
            ->with(typeOf('array'))
            ->once()
            ->andReturn(Mockery::self());

        $mock->shouldReceive('GET->getBody')
            ->once()
            ->andReturn($responseData);

        return $mock;

    }

    protected function fetchTokenCredentials()
    {
        $clientCredentials = [
            'client_id'     => 'dummy-client-id',
            'client_secret' => 'dummy-client-secret',
            'redirect'      => 'http://www.mywebapp.com/galleries'
        ];

        $this->auth
            ->shouldReceive('init')
            ->with($clientCredentials)
            ->once()
            ->andReturn($this->auth)
            ->shouldReceive('authorize')
            ->with('http://www.mockservice.com/auth/authorize')
            ->once()
            ->andReturn($this->auth)
            ->shouldReceive('access')
            ->with('http://www.mockservice.com/auth/access')
            ->once()
            ->andReturn($this->auth)
            ->shouldReceive('finally')
            ->with(Mockery::type('callable'))
            ->once()
            ->andReturn(Mockery::self());

        parent::fetchTokenCredentials();

        return [
            'client_id'     => 'dummy-client-id',
            'client_secret' => 'dummy-client-secret',
            'redirect'      => 'http://www.mywebapp.com/galleries',
            'access_token'  => 'dummy-access-token',
            'expires'       => 'dummy-expires-date'

        ];

    }

}
