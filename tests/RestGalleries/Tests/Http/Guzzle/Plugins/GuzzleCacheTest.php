<?php namespace RestGalleries\Tests\Http\Guzzle\Plugins;

use RestGalleries\Http\Guzzle\Plugins\GuzzleCache;

class GuzzleCacheTest extends \RestGalleries\Tests\TestCase
{
    public function testAddArraySystemReturnsCorrectObject()
    {
        $plugin     = new GuzzleCache('array');
        $subscriber = $plugin->add();

        assertThat($subscriber, is(anInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface')));

    }

    public function testAddFileSystemReturnsCorrectObject()
    {
        $plugin     = new GuzzleCache('file');
        $subscriber = $plugin->add();

        assertThat($subscriber, is(anInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface')));

    }

}
