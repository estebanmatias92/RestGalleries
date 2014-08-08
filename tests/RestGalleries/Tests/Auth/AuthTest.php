<?php namespace RestGalleries\Tests\Auth;

use Mockery;

class AuthTest extends \RestGalleries\Tests\TestCase
{
    public function testNewRequestReturnsCorrectObject()
    {
        $auth    = new AuthStub;
        $request = $auth->newRequest();

        assertThat($request, is(anInstanceOf('RestGalleries\Http\RequestAdapter')));

    }

    public function testNewAuthExtensionReturnsCorrectObject()
    {
        $auth          = new AuthStub;
        $authExtension = $auth->newAuthExtension();

        assertThat($authExtension, is(anInstanceOf('RestGalleries\Http\Plugins\RequestPluginAdapter')));

    }

    public function testGetOauth1KeysReturnsCorrectKeys()
    {
        $credentialKeys = [
            'client_credentials' => ['consumer_key', 'consumer_secret', 'callback'],
            'token_credentials' => ['consumer_key', 'consumer_secret', 'token', 'token_secret']
        ];

        $auth = new AuthStub;
        $keys = $auth->getOauth1Keys();

        assertThat($keys, is(equalTo($credentialKeys)));

    }

    public function testGetOauth2KeysReturnsCorrectKeys()
    {
        $credentialKeys = [
            'client_credentials' => ['client_id', 'client_secret', 'callback'],
            'token_credentials' => ['access_token', 'expires']
        ];

        $auth = new AuthStub;
        $keys = $auth->getOauth2Keys();

        assertThat($keys, is(equalTo($credentialKeys)));
    }

    public function testGetAuthProtocolReturnsOauth1Protocol()
    {
        $clientCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'callback'        => 'http://www.mywebapp.com/galleries'
        ];

        $clientExtraCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'callback'        => 'http://www.mywebapp.com/galleries',
            'extra_api_data'  => 'I don\'t know.'
        ];

        $tokenCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'token'           => 'dummy-token',
            'token_secret'    => 'dummy-token-secret'
        ];

        $tokenExtraCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'token'           => 'dummy-token',
            'token_secret'    => 'dummy-token-secret',
            'extra_api_data'  => 'I don\'t know.'
        ];

        $protocol  = AuthStub::getAuthProtocol($clientCredentials);
        $protocol2 = AuthStub::getAuthProtocol($clientExtraCredentials);
        $protocol3 = AuthStub::getAuthProtocol($tokenCredentials);
        $protocol4 = AuthStub::getAuthProtocol($tokenExtraCredentials);

        assertThat($protocol, is(equalTo('oauth1')));
        assertThat($protocol2, is(equalTo('oauth1')));
        assertThat($protocol3, is(equalTo('oauth1')));
        assertThat($protocol4, is(equalTo('oauth1')));

    }

    public function testGetAuthProtocolReturnsOauth2Protocol()
    {
        $clientCredentials = [
            'client_id'     => 'dummy-client-id',
            'client_secret' => 'dummy-client-secret',
            'callback'      => 'http://www.mywebapp.com/galleries'
        ];

        $clientExtraCredentials = [
            'client_id'      => 'dummy-client-id',
            'client_secret'  => 'dummy-client-secret',
            'callback'       => 'http://www.mywebapp.com/galleries',
            'extra_api_data' => 'I don\'t know.'
        ];

        $tokenCredentials =  [
            'access_token' => 'dummy-access-token',
            'expires'      => 'dummy-expires-date'
        ];

        $tokenExtraCredentials = [
            'access_token'   => 'dummy-access-token',
            'expires'        => 'dummy-expires-date',
            'extra_api_data' => 'I don\'t know.'
        ];

        $protocol  = AuthStub::getAuthProtocol($clientCredentials);
        $protocol2 = AuthStub::getAuthProtocol($clientExtraCredentials);
        $protocol3 = AuthStub::getAuthProtocol($tokenCredentials);
        $protocol4 = AuthStub::getAuthProtocol($tokenExtraCredentials);

        assertThat($protocol, is(equalTo('oauth2')));
        assertThat($protocol2, is(equalTo('oauth2')));
        assertThat($protocol3, is(equalTo('oauth2')));
        assertThat($protocol4, is(equalTo('oauth2')));

    }

    public function testGetAuthProtocolInvalidCredentialKeys()
    {
        $prefixedCredentials = [
            'oauth_consumer_key'    => 'dummy-consumer-key',
            'oauth_consumer_secret' => 'dummy-consumer-secret',
            'oauth_callback'        => 'http://www.mywebapp.com/galleries'
        ];

        $incompleteCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret'
        ];

        $mixedProtocolCredentials = [
            'consumer_key'  => 'dummy-consumer-key',
            'client_secret' => 'dummy-client-secret',
            'callback'      => 'http://www.mywebapp.com/galleries'
        ];

        $protocol  = AuthStub::getAuthProtocol($prefixedCredentials);
        $protocol2 = AuthStub::getAuthProtocol($incompleteCredentials);
        $protocol3 = AuthStub::getAuthProtocol($mixedProtocolCredentials);

        assertThat($protocol, is(equalTo(false)));
        assertThat($protocol2, is(equalTo(false)));
        assertThat($protocol3, is(equalTo(false)));

    }

    public function testConnectReturnsObject()
    {
        $clientCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'callback'        => 'http://www.mywebapp.com/galleries'
        ];

        $endPoints = [
            'request'   => 'http://www.mockservice.com/auth/request',
            'authorize' => 'http://www.mockservice.com/auth/authorize',
            'access'    => 'http://www.mockservice.com/auth/access'
        ];

        $userData = AuthFetchTokenCredentialsStub::connect($clientCredentials, $endPoints, 'http://www.mockservice.com/rest/user');

        assertThat($userData->tokens, set('consumer_key'));
        assertThat($userData->tokens, set('consumer_secret'));
        assertThat($userData->tokens, set('token'));
        assertThat($userData->tokens, set('token_secret'));

    }

    public function testConnectInvalidCredentials()
    {
        $this->setExpectedException('InvalidArgumentException', 'Credential keys are invalid.');

        $clientCredentials = [
            'oauth_consumer_key'    => 'dummy-consumer-key',
            'oauth_consumer_secret' => 'dummy-consumer-secret',
            'oauth_callback'        => 'http://www.mywebapp.com/galleries'
        ];

        $endPoints = [
            'request'   => 'http://www.mockservice.com/auth/request',
            'authorize' => 'http://www.mockservice.com/auth/authorize',
            'access'    => 'http://www.mockservice.com/auth/access'
        ];

        AuthStub::connect($clientCredentials, $endPoints, 'http://www.mockservice.com/rest/user');

    }

    public function testVerifyCredentialsReturnsObject()
    {
        $tokenCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'token'           => 'dummy-token',
            'token_secret'    => 'dummy-token-secret'
        ];

        $userData = AuthFetchTokenCredentialsStub::verifyCredentials($tokenCredentials,  'http://www.mockservice.com/rest/user');

        assertThat($userData->tokens, set('consumer_key'));
        assertThat($userData->tokens, set('consumer_secret'));
        assertThat($userData->tokens, set('token'));
        assertThat($userData->tokens, set('token_secret'));

    }

    public function testVerifyCredentialsInvalidCredentials()
    {
        $this->setExpectedException('InvalidArgumentException', 'Credential keys are invalid.');

        $tokenCredentials = [
            'oauth_consumer_key'    => 'dummy-consumer-key',
            'oauth_consumer_secret' => 'dummy-consumer-secret',
            'oauth_token'           => 'dummy-token',
            'oauth_token_secret'    => 'dummy-token-secret'
        ];

        AuthStub::verifyCredentials($tokenCredentials, 'http://www.mockservice.com/rest/user');

    }

}

class AuthStub extends \RestGalleries\Auth\Auth
{
    public function __construct()
    {
        $this->auth = Mockery::mock('FakeAuthClient');
    }

    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\RequestAdapter');

        return parent::newRequest($mock);

    }

    public function newAuthExtension(\RestGalleries\Http\Plugins\RequestPluginAdapter $authExtension = null)
    {
        $tokenCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'token'           => 'dummy-token',
            'token_secret'    => 'dummy-token-secret'
        ];

        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\Plugins\\GuzzleAuth', [$tokenCredentials]);

        return parent::newAuthExtension($mock);

    }

    protected function fetchTokenCredentials() {}

}

class AuthFetchTokenCredentialsStub extends AuthStub
{
    public function newRequest(\RestGalleries\Http\RequestAdapter $request = null)
    {
        $responseData = new \stdClass;
        $responseData->whateverUserData = 'some-fake-user-data';
        $responseData->tokens = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'token'           => 'dummy-token',
            'token_secret'    => 'dummy-token-secret'
        ];

        $mock = parent::newRequest();
        $mock->shouldReceive('init')
            ->with('http://www.mockservice.com/rest/user')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('addPlugins')
            ->with(typeOf('array'))
            ->andReturn(Mockery::self())
            ->shouldReceive('GET')
            ->once()
            ->andReturn(Mockery::self())
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($responseData);

        return $mock;

    }

    protected function fetchTokenCredentials()
    {
        $clientCredentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'callback'        => 'http://www.mywebapp.com/galleries'
        ];

        $endPoints = [
            'request'   => 'http://www.mockservice.com/auth/request',
            'authorize' => 'http://www.mockservice.com/auth/authorize',
            'access'    => 'http://www.mockservice.com/auth/access'
        ];

        $tokens = [
            'token'        => 'dummy-token',
            'token_secret' => 'dummy-token-secret'
        ];

        $this->auth
            ->shouldReceive('setClientCredentials')
            ->with($clientCredentials)
            ->once()
            ->andReturn($this->auth)
            ->shouldReceive('setAuthEndPoints')
            ->with($endPoints)
            ->once()
            ->andReturn($this->auth)
            ->shouldReceive('getTokens')
            ->once()
            ->andReturn($tokens);

        return $this->auth
            ->setClientCredentials($this->credentials)
            ->setAuthEndPoints($this->endPoints)
            ->getTokens();

    }

}
