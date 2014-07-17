<?php namespace RestGalleries\Http\Plugins;

use RestGalleries\Http\Plugins\RequestPluginAdapter;

abstract class Cache implements RequestPluginAdapter
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

    public function __construct($system = 'file', array $path = array())
    {
        $this->processPluginData($system, $path);
    }

    /**
     * [processPluginData description]
     *
     * @param  [type] $system
     * @param  [type] $path
     * @return [type]
     */
    protected function processPluginData($system, $path)
    {
        if (! $this->isValidCacheSystem($system)) {
            throw new \InvalidArgumentException('Cache system is invalid.');
        }

        $this->system = $system;

        if (empty($path)) {
            $path = [
                'folder' => dirname(dirname(dirname(__FILE__))) . '/storage/cache',
            ];
        }

        $this->path = $path;
    }

    /**
     * Takes a cache system name, selects it and stores it.
     *
     * @param  string $system
     * @param  array  $path
     * @throws InvalidArgumentException
     */
    public function add()
    {
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
