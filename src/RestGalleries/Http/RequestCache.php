<?php namespace RestGalleries\Http;

abstract class RequestCache
{
    /**
     * [$system description]
     *
     * @var string
     */
    protected $system;

    /**
     * [$path description]
     *
     * @var array
     */
    protected $path;

    /**
     * Takes a cache system name, selects it and stores it.
     *
     * @param  string $system
     * @param  array  $path
     * @throws InvalidArgumentException
     */
    public static function addCache($system = 'file', array $path = array())
    {
        if (! $this->isValidCacheSystem($system)) {
            throw new \InvalidArgumentException('Cache system is invalid. ' . __METHOD__);
        }

        if (empty($path)) {
            $path = [
                'folder' => dirname(dirname(dirname(__FILE__))) . '/storage/cache',
            ];
        }

        $this->system = $system;
        $this->path   = $path;

        return $this->getCacheExtension();

    }

    protected function isValidCacheSystem($system)
    {
        $systems = ['array', 'file'];

        if (! in_array($system, $systems)) {
            return false;
        }

        return true;

    }

    protected function getCacheExtension()
    {
        $method = 'get';
        $method .= ucfirst($this->system);
        $method .= 'System';

        return call_user_func_array([$this, $method], [null]);

    }

    abstract protected function getArraySystem();
    abstract protected function getFileSystem();

}
