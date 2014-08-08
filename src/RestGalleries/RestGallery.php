<?php namespace RestGalleries;

use RestGalleries\Http\Guzzle\Plugins\GuzzleAuth;
use RestGalleries\Http\Guzzle\Plugins\GuzzleCache;
use RestGalleries\Interfaces\GalleryAdapter;

/**
 * Abstract model CRUD-type to simplify the interaction with api services.
 */
abstract class RestGallery
{
    /**
     * Api service name.
     *
     * @var string
     */
    protected $service;

    /**
     * Credentials to connect with an api service,
     *
     * @var array
     */
    protected $clientCredentials = [];

    /**
     * [$auth description]
     *
     * @var array
     */
    protected $plugins = [];

    /**
     * Returns all galleries (and its photos) from an api service.
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function all()
    {
        return $this->newGallery()->all();
    }

    /**
     * Finds and returns a specific gallery from an api service.
     *
     * @param  string  $id
     * @return \Illuminate\Support\Fluent|null
     */
    public function find($id)
    {
        return $this->newGallery()->find($id);
    }

    /**
     * This method create a new service gallery object to interact with the external api service.
     *
     * @return RestGalleries\APIs\<service>\Gallery
     */
    public function newGallery(GalleryAdapter $gallery = null)
    {
        if (empty($gallery)) {
            $class   = $this->getNamespaceService() . 'Gallery';
            $gallery = new $class;
        }

        if (! empty($this->plugins)) {
            array_walk($this->plugins, [$gallery, 'addPlugin']);
        }

        return $gallery;

    }

    private function getNamespaceService()
    {
        $service = get_class_namespace($this);
        $service .= '\\APIs\\';
        $service .= $this->getService();
        $service .= '\\';

        return $service;
    }

    /**
     * Sets the current service api name to interact with it.
     *
     * @param  string  $service
     * @return void
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * If service name is configured, return it, but returns the child class name for the service api name.
     *
     * @return string
     */
    public function getService()
    {
        if (isset($this->service)) {
            return $this->service;
        }

        return class_basename($this);

    }

    /**
     * Makes the authentication for the requests,
     *
     * @param  array  $tokenCredentials
     * @return object
     */
    public function setAuth(array $tokenCredentials)
    {
        $this->plugins['auth'] = new GuzzleAuth($tokenCredentials);
        return $this;
    }

    /**
     * [setCache description]
     *
     * @param [type] $system
     * @param array  $path
     */
    public function setCache($system, array $path = array())
    {
        $this->plugins['cache'] = new GuzzleCache($system, $path);
        return $this;
    }

    /**
     * [getCache description]
     *
     * @return [type]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Takes account credentials and calls the user object to make the requests to get the api authorization.
     *
     * @return \Illuminate\Support\Fluent
     */
    public static function connect()
    {
        $instance = new static;
        $user     = $instance->newUser();

        return $user->connect($instance->clientCredentials);

    }

    /**
     * Investigates if the access tokens still are valid. calling the user object and making the respective requests,
     *
     * @param  array  $tokenCredentials
     * @return \Illuminate\Support\Fluent
     */
    public static function verifyCredentials(array $tokenCredentials)
    {
        $instance = new static;
        $user     = $instance->newUser();

        return $user->verifyCredentials($tokenCredentials);

    }

    /**
     * Creates new user object that will interact with the api service.
     *
     * @return RestGalleries\APIs\<service>\User
     */
    public function newUser()
    {
        $class = $this->getNamespaceService() . 'User';
        return new $class;
    }

}
