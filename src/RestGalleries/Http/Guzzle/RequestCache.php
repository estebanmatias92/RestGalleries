<?php namespace RestGalleries\Http\Guzzle;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;
use RestGalleries\Http\RequestCache;

class GuzzleResquestCache extends RequestCache
{
    /**
     * Returns the Array cache system.
     *
     * @return ArrayCache
     */
    protected function getArraySystem()
    {
        return new CachePlugin([
            'adapter' => new DoctrineCacheAdapter(new ArrayCache())
        ]);

    }

    /**
     * Returns the file cache system.
     *
     * @param  array           $path
     * @return FilesystemCache
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
