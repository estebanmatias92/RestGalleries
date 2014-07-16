<?php namespace RestGalleries\Tests\APIs;

use Mockery;

class ApiUserTest extends \RestGalleries\Tests\TestCase
{
    public function testNewAuthReturnsAuthObject()
    {
        $model = new \RestGalleries\Tests\APIs\StubService\User;
        $auth  = $model->newAuth();

        assertThat($auth, is(anInstanceOf('RestGalleries\Auth\OhmyAuth\OhmyAuth')));

    }

    public function testConnectReturnsObject()
    {
        $auth = new StubServiceUserConnectStub;
        $user = $auth->connect(['valid-oauth1-credentials']);

        assertThat($user, is(anInstanceOf('Illuminate\Support\Fluent')));

    }

    public function testConnectPropertiesReturnedObject()
    {
        $auth = new StubServiceUserConnectStub;
        $user = $auth->connect(['valid-oauth1-credentials']);

        assertThat($user, set('id'));
        assertThat($user, set('realname'));
        assertThat($user, set('username'));
        assertThat($user, set('consumer_key'));
        assertThat($user, set('consumer_secret'));
        assertThat($user, set('token'));
        assertThat($user, set('token_secret'));

    }

    public function testConnectFails()
    {
        $auth = new StubServiceUserConnectFailsStub;
        $user = $auth->connect(['invalid-oauth1-credentials']);

        assertThat($user, is(equalTo(false)));

    }


}

class StubServiceUserStub extends \RestGalleries\Tests\APIs\StubService\User
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $mock = Mockery::mock('RestGalleries\\Auth\\OhmyAuth\\OhmyAuth');

        return $mock;

    }
}

class StubServiceUserConnectStub extends StubServiceUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/user/';

        $clientCredentials = ['valid-oauth1-credentials'];

        $endPoints = [
            'request'   => $this->urlRequest,
            'authorize' => $this->urlAuthorize,
            'access'    => $this->urlAccess
        ];

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

class StubServiceUserConnectFailsStub extends StubServiceUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $responsesDir = __DIR__ . '/StubService/responses/user/';

        $clientCredentials = ['invalid-oauth1-credentials'];

        $endPoints = [
            'request'   => $this->urlRequest,
            'authorize' => $this->urlAuthorize,
            'access'    => $this->urlAccess
        ];

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
