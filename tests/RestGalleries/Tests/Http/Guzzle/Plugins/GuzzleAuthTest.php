<?php namespace RestGalleries\Tests\Http\Guzzle\Plugins;

use RestGalleries\Http\Guzzle\Plugins\GuzzleAuth;

class GuzzleAuthTest extends \RestGalleries\Tests\TestCase
{
    public function testAddOauth1ExtensionReturnsCorrectObject()
    {
        $credentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'token'           => 'dummy-token',
            'token_secret'    => 'dummy-token-secret'
        ];

        $plugin     = new GuzzleAuth($credentials);
        $subscriber = $plugin->add();

        assertThat($subscriber, is(anInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface')));

    }

    public function testAddOauth2ExtensionReturnsCorrectObject()
    {
        $credentials = [
            'access_token' => 'dummy-access-token',
            'expires'      => 'dummy-expires'
        ];

        $plugin     = new GuzzleAuth($credentials);
        $subscriber = $plugin->add();

        assertThat($subscriber, is(anInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface')));

    }
}
