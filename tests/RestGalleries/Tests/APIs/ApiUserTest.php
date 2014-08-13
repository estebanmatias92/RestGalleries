<?php namespace RestGalleries\Tests\APIs;

use Mockery;
use RestGalleries\Tests\APIs\StubService\User;

class ApiUserTest extends \RestGalleries\Tests\TestCase
{
    public function testNewAuthReturnsCorrectObject()
    {
        $user = new \RestGalleries\Tests\APIs\StubService\User;
        $auth = $user->newAuth();

        assertThat($auth, is(anInstanceOf('RestGalleries\Auth\AuthAdapter')));

    }

    public function testConnectReturnsCorrectObject()
    {
        $user     = new ServiceUserConnectStub;
        $userData = $user->connect(['valid-client-credentials']);

        assertThat($userData, is(anInstanceOf('Illuminate\Support\Fluent')));

    }

    public function testConnectReturnedObject()
    {
        $user     = new ServiceUserConnectStub;
        $userData = $user->connect(['valid-client-credentials']);

        assertThat($userData, set('id'));
        assertThat($userData, set('realname'));
        assertThat($userData, set('username'));
        assertThat($userData, set('url'));
        assertThat($userData, set('credentials'));
        assertThat($userData->credentials, hasKey('consumer_key'));
        assertThat($userData->credentials, hasKey('consumer_secret'));
        assertThat($userData->credentials, hasKey('token'));
        assertThat($userData->credentials, hasKey('token_secret'));

    }

    public function testConnectFails()
    {
        $this->setExpectedException('RestGalleries\\Exception\\AuthException', 'The credentials are not valid or are obsolete.');

        $user = new ServiceUserConnectFailsStub;
        $user->connect(['invalid-client-credentials']);

    }

    public function testVerifyCredentialsReturnsCorrectObject()
    {
        $user     = new ServiceUserVerifyCredentialsStub;
        $userData = $user->verifyCredentials(['valid-token-credentials']);

        assertThat($userData, is(anInstanceOf('Illuminate\Support\Fluent')));

    }

    public function testVerifyCredentialsReturnedObject()
    {
        $user     = new ServiceUserVerifyCredentialsStub;
        $userData = $user->verifyCredentials(['valid-token-credentials']);

        assertThat($userData, set('id'));
        assertThat($userData, set('realname'));
        assertThat($userData, set('username'));
        assertThat($userData, set('url'));
        assertThat($userData, set('credentials'));
        assertThat($userData->credentials, hasKey('consumer_key'));
        assertThat($userData->credentials, hasKey('consumer_secret'));
        assertThat($userData->credentials, hasKey('token'));
        assertThat($userData->credentials, hasKey('token_secret'));

    }

    public function testVerifyCredentialsFails()
    {
        $this->setExpectedException('RestGalleries\\Exception\\AuthException', 'The credentials are not valid or are obsolete.');

        $user = new ServiceUserVerifyCredentialsFailsStub;
        $user->verifyCredentials(['invalid-token-credentials']);

    }

}

class ServiceUserStub extends User
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $mock = Mockery::mock('RestGalleries\\Auth\\AuthAdapter');

        return parent::newAuth($mock);

    }
}

class ServiceUserConnectStub extends ServiceUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $clientCredentials = ['valid-client-credentials'];

        $endPoints = [
            'request'   => $this->urlRequest,
            'authorize' => $this->urlAuthorize,
            'access'    => $this->urlAccess
        ];

        $responsesDir = __DIR__ . '/StubService/responses/user/';
        $responseFile = $responsesDir . 'mockservice-rest-user.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newAuth();
        $mock->shouldReceive('connect')
            ->with($clientCredentials, $endPoints, $this->checkUrl)
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class ServiceUserConnectFailsStub extends ServiceUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $clientCredentials = ['invalid-client-credentials'];

        $endPoints = [
            'request'   => $this->urlRequest,
            'authorize' => $this->urlAuthorize,
            'access'    => $this->urlAccess
        ];

        $responsesDir = __DIR__ . '/StubService/responses/user/';
        $responseFile = $responsesDir . 'mockservice-rest-user-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newAuth();
        $mock->shouldReceive('connect')
            ->with($clientCredentials, $endPoints, $this->checkUrl)
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class ServiceUserVerifyCredentialsStub extends ServiceUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $clientCredentials = ['valid-token-credentials'];

        $responsesDir = __DIR__ . '/StubService/responses/user/';
        $responseFile = $responsesDir . 'mockservice-rest-user.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newAuth();
        $mock->shouldReceive('verifyCredentials')
            ->with($clientCredentials, $this->checkUrl)
            ->once()
            ->andReturn($responseBody);

        return $mock;
    }

}

class ServiceUserVerifyCredentialsFailsStub extends ServiceUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $clientCredentials = ['invalid-token-credentials'];

        $responsesDir = __DIR__ . '/StubService/responses/user/';
        $responseFile = $responsesDir . 'mockservice-rest-user-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newAuth();
        $mock->shouldReceive('verifyCredentials')
            ->with($clientCredentials, $this->checkUrl)
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}
