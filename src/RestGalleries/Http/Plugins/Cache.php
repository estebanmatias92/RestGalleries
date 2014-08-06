<?php namespace RestGalleries\Http\Plugins;

use RestGalleries\Http\Plugins\RequestPluginAdapter;

/**
 * Normalizes the cache plugin of the Http client, receives input data to configure the plugin, and adds several by default.
 */
abstract class Cache implements RequestPluginAdapter
{
    /**
     * Stores the cache system name.
     *
     * @var string
     */
    protected $system;

    /**
     * Stores the cache path as an array.
     *
     * @var array
     */
    protected $path;

    /**
     * Initializes processing of plugin data.
     *
     * @param  string $system
     * @param  array  $path
     * @return void
     */
    public function __construct($system = 'file', array $path = array())
    {
        $this->processPluginData($system, $path);
    }

    /**
     * Receives data to configure the plugin, then verifies and separates them into variables.
     *
     * @param  string $system
     * @param  array  $path
     * @throws \InvalidArgumentException
     * @return void
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
     * Verifies if an string match some cache system.
     *
     * @param  string $system
     * @return boolean
     */
    protected function isValidCacheSystem($system)
    {
        $systems = ['array', 'file'];

        if (! in_array($system, $systems)) {
            return false;
        }

        return true;

    }

    /**
     * Calls another method that determine which plugin will be returned and return it.
     *
     * @return object
     */
    public function add()
    {
        return $this->getCacheExtension();
    }

    /**
     * Calls method that will return the cache system that specified in the property $system.
     *
     * @return object
     */
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
