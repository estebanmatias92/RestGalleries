<?php namespace RestGalleries\Http\Guzzle\Plugins;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;
use RestGalleries\Http\Plugins\Cache;

/**
 * Adapter class for cache plugins from the Http client 'Guzzle'.
 */
class GuzzleCache extends Cache
{
    /**
     * Returns cache plugin for the cache system 'array'.
     *
     * @return \Doctrine\Common\Cache\ArrayCache
     */
    protected function getArraySystem()
    {
        return new CachePlugin([
            'adapter' => new DoctrineCacheAdapter(new ArrayCache())
        ]);

    }

    /**
     * Returns cache plugin for the cache system 'file'.
     *
     * @return \Doctrine\Common\Cache\FilesystemCache
     */
    protected function getFileSystem()
    {
        return new CachePlugin([
            'storage' => new DefaultCacheStorage(
                new DoctrineCacheAdapter(
                    new FilesystemCache($this->path['folder'])
                )
            )
        ]);

    }

}
