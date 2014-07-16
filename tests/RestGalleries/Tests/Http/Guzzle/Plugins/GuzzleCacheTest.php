<?php namespace RestGalleries\Tests\Http\Guzzle\Plugins;

use RestGalleries\Http\Guzzle\Plugins\GuzzleCache;

class GuzzleCacheTest extends \RestGalleries\Tests\TestCase
{
    public function testAddArraySystemReturnsCorrectObject()
    {
        $plugin = GuzzleCache::add('array');

        assertThat($plugin, is(anInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface')));

    }

    public function testAddFileSystemReturnsCorrectObject()
    {
        $plugin = GuzzleCache::add('file');

        assertThat($plugin, is(anInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface')));

    }

}
