<?php namespace RestGalleries\Http\Plugins;

abstract class Cache
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
    public static function add($system = 'file', array $path = array())
    {
        $instance = new static;

        if (! $instance->isValidCacheSystem($system)) {
            throw new \InvalidArgumentException('Cache system is invalid.');
        }

        if (empty($path)) {
            $path = [
                'folder' => dirname(dirname(dirname(__FILE__))) . '/storage/cache',
            ];
        }

        $instance->system = $system;
        $instance->path   = $path;

        return $instance->getCacheExtension();

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
