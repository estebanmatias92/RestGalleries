<?php namespace RestGalleries\Tests\Http\Plugins;



class AuthTest extends \RestGalleries\Tests\TestCase
{
    public function testAddOauth1Extension()
    {
        $credentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'token'           => 'dummy-token',
            'token_secret'    => 'dummy-token-secret'
        ];

        $plugin     = new AuthStub($credentials);
        $plugin     = $plugin->add();
        $pluginName = $plugin->name;

        assertThat($pluginName, is(equalTo('OAuth1.0')));

    }

    public function testAddOauth2Extension()
    {
        $credentials = [
            'access_token' => 'dummy-access-token',
            'expires'      => 'dummy-expires'
        ];

        $plugin     = new AuthStub($credentials);
        $plugin     = $plugin->add();
        $pluginName = $plugin->name;

        assertThat($pluginName, is(equalTo('OAuth2.0')));

    }

    public function testAddInvalidAuthExtension()
    {
        $this->setExpectedException(
            'InvalidArgumentException', 'Credentials are invalid.'
        );

        $credentials = ['any-invalid-crendential'];

        new AuthStub($credentials);

    }

}


class AuthStub extends \RestGalleries\Http\Plugins\Auth
{
    protected function getOauth1Extension()
    {
        $plugin       = new \stdClass;
        $plugin->name = 'OAuth1.0';

        return $plugin;

    }

    protected function getOauth2Extension()
    {
        $plugin       = new \stdClass;
        $plugin->name = 'OAuth2.0';

        return $plugin;

    }

}
