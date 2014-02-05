<?php

namespace RestGalleries\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;

/**
 * RestCache description.
 */
class RestCache
{
    private $client;
    private $dev_mode = false;
    private $path;

    public function __construct(&$client)
    {
        $this->client = $client;
        $this->setCacheFolder();
    }

    public function setCacheFolder($path = null)
    {
        $this->path = isset($path) ? $path : dirname(dirname(__FILE__)) . '/storage/cache';
    }

    public function setDevMode($mode)
    {
        $this->dev_mode = $mode;
    }

    public function make()
    {
        if (true == $this->dev_mode) {
            $cache_plugin = new CachePlugin(array(
                'adapter' => new DoctrineCacheAdapter(new ArrayCache())
            ));
        } else {
            $cache_plugin = new CachePlugin(array(
                'storage' => new DefaultCacheStorage(
                    new DoctrineCacheAdapter(
                        new FilesystemCache($this->path)
                    )
                )
            ));
        }

        $this->client->addSubscriber($cache_plugin);
    }
}
