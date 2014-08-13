<?php namespace RestGalleries\Tests\APIs\Flickr;

use Mockery;
use RestGalleries\APIs\Flickr\User;

class UserTest extends \RestGalleries\Tests\TestCase
{
    protected $url = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken&format=json&nojsoncallback=1';

    public function testConnectReturnedObject()
    {
        $user     = new FlickrUserConnectStub;
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
        $this->setExpectedException('RestGalleries\\Exception\\AuthException');

        $user = new FlickrUserConnectFailsStub;
        $user->connect(['invalid-client-credentials']);

    }

    public function testVerifyCredentialsReturnedObject()
    {
        $user     = new FlickrUserVerifyCredentialsStub;
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
        $this->setExpectedException('RestGalleries\\Exception\\AuthException');

        $user     = new FlickrUserVerifyCredentialsFailsStub;
        $userData = $user->verifyCredentials(['invalid-token-credentials']);

    }

}

class FlickrUserStub extends User
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $mock = Mockery::mock('RestGalleries\\Auth\\OhmyAuth\\OhmyAuth');

        return parent::newAuth($mock);

    }
}

class FlickrUserConnectStub extends FlickrUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $clientCredentials = ['valid-client-credentials'];
        $endPoints = [
            'request'   => 'https://www.flickr.com/services/oauth/request_token',
            'authorize' => 'https://www.flickr.com/services/oauth/authorize',
            'access'    => 'https://www.flickr.com/services/oauth/access_token'
        ];
        $checkUrl = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken&format=json&nojsoncallback=1';

        $responsesDir = __DIR__ . '/responses/user/';
        $responseFile = $responsesDir . 'flickrauth-oauth-checktoken.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newAuth();
        $mock->shouldReceive('connect')
            ->with($clientCredentials, $endPoints, $checkUrl)
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class FlickrUserConnectFailsStub extends FlickrUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $clientCredentials = ['invalid-client-credentials'];
        $endPoints = [
            'request'   => 'https://www.flickr.com/services/oauth/request_token',
            'authorize' => 'https://www.flickr.com/services/oauth/authorize',
            'access'    => 'https://www.flickr.com/services/oauth/access_token'
        ];
        $checkUrl = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken&format=json&nojsoncallback=1';

        $responsesDir = __DIR__ . '/responses/user/';
        $responseFile = $responsesDir . 'flickrauth-oauth-checktoken-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newAuth();
        $mock->shouldReceive('connect')
            ->with($clientCredentials, $endPoints, $checkUrl)
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class FlickrUserVerifyCredentialsStub extends FlickrUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $clientCredentials = ['valid-token-credentials'];
        $checkUrl          = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken&format=json&nojsoncallback=1';

        $responsesDir = __DIR__ . '/responses/user/';
        $responseFile = $responsesDir . 'flickrauth-oauth-checktoken.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newAuth();
        $mock->shouldReceive('verifyCredentials')
            ->with($clientCredentials, $checkUrl)
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}

class FlickrUserVerifyCredentialsFailsStub extends FlickrUserStub
{
    public function newAuth(\RestGalleries\Auth\AuthAdapter $auth = null)
    {
        $clientCredentials = ['invalid-token-credentials'];
        $checkUrl          = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken&format=json&nojsoncallback=1';

        $responsesDir = __DIR__ . '/responses/user/';
        $responseFile = $responsesDir . 'flickrauth-oauth-checktoken-fail.json';
        $responseBody = json_decode(file_get_contents($responseFile));

        $mock = parent::newAuth();
        $mock->shouldReceive('verifyCredentials')
            ->with($clientCredentials, $checkUrl)
            ->once()
            ->andReturn($responseBody);

        return $mock;

    }

}
