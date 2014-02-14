<?php

namespace RestGalleries\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;

/**
 * Helper class for adding cache to the Guzzle client.
 */
class RestCache
{
    private $client;
    private $developmentMode = false;
    private $path;

    /**
     * Takes the Guzzle client object as parameter(reference) and set default cache folder.
     *
     * @param   object           $client   Guzzle client object.
     */
    public function __construct(&$client)
    {
        $this->client = $client;
        $this->setCacheFolder();
    }

    /**
     * Sets the cache folder.
     *
     * @param   string           $path   Cache folder path.
     */
    public function setCacheFolder($path = null)
    {
        $this->path = isset($path) ? $path : dirname(dirname(__FILE__)) . '/storage/cache';
    }

    /**
     * Sets dev mode for using different cache system (array or filesystem).
     *
     * @param   boolean          $mode   Boolean value, if its true the system cache to use will be an array, if its false, will use a filesystem cache.
     */
    public function setDevelopmentMode($mode)
    {
        $this->developmentMode = $mode;
    }

    /**
     * The magic its here, Guzzle cache system is applied for this function to the Guzzle client object.
     */
    public function make()
    {
        if (true == $this->developmentMode) {
            $cachePlugin = new CachePlugin(array(
                'adapter' => new DoctrineCacheAdapter(new ArrayCache())
            ));
        } else {
            $cachePlugin = new CachePlugin(array(
                'storage' => new DefaultCacheStorage(
                    new DoctrineCacheAdapter(
                        new FilesystemCache($this->path)
                    )
                )
            ));
        }

        $this->client->addSubscriber($cachePlugin);
    }

}
