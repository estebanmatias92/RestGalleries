<?php namespace RestGalleries;

use RestGalleries\Factory;

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
     * Object to make the api requests.
     *
     * @var object
     */
    protected $query;

    /**
     * Credentials to authenticate every api request,
     *
     * @var array
     */
    protected $credentials = [];

    /**
     * Returns all galleries (and its photos) from an api service.
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function all()
    {
        return $this->query->all();
    }

    /**
     * Finds and returns a specific gallery from an api service.
     *
     * @param  string  $id
     * @return \Illuminate\Support\Fluent|null
     */
    public function find($id)
    {
        return $this->query->find($id);
    }

    /**
     * Makes the authentication for the requests,
     *
     * @param  array  $tokenCredentials
     * @return object
     */
    public static function authenticate(array $tokenCredentials)
    {
        $instance        = new static;
        $query           = $instance->newQuery();
        $instance->query = &$query;

        $instance->setCredentials($tokenCredentials);
        $query->setAuth($instance->getCredentials());

        return $instance;

    }

    /**
     * This method create a new service gallery object to interact with the external api service.
     *
     * @return RestGalleries\APIs\<service>\Gallery
     */
    public function newQuery()
    {
        $class = $this->getService() . '\\Gallery';

        return Factory::make($class);

    }

    /**
     * Takes account credentials and calls the user object to make the requests to get the api authorization.
     *
     * @param  array  $clientCredentials
     * @return \Illuminate\Support\Fluent
     */
    public static function connect(array $clientCredentials)
    {
        $instance = new static;
        $auth     = $instance->newAuthentication();

        $instance->setCredentials($clientCredentials);

        return $auth->connect($instance->getCredentials());

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
        $auth     = $instance->newAuthentication();

        $instance->setCredentials($tokenCredentials);

        return $auth->verifyCredentials($instance->getCredentials());

    }

    /**
     * Creates new user object that will interact with the api service.
     *
     * @return RestGalleries\APIs\<service>\User
     */
    public function newAuthentication()
    {
        $class = $this->getService() . '\\User';

        return Factory::make($class);

    }

    /**
     * Returns current credential array.
     *
     * @return array
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Takes adds and cleans credentials of an account.
     *
     * @param  array  $credentials
     * @return void
     */
    public function setCredentials(array $credentials)
    {
        $this->credentials = $this->addCredentials($credentials);
        $this->credentials = $this->cleanCredentials();
    }

    /**
     * Adds more credentials to the current credential array.
     *
     * @param  array  $credentials
     * @return array
     */
    private function addCredentials($credentials)
    {
        return array_merge($this->getCredentials(), $credentials);
    }

    /**
     * Cleans current credential array of empty values and repeated keys.
     *
     * @return array
     */
    private function cleanCredentials()
    {
        $filtered = array_filter($this->getCredentials());

        return array_unique_keys($filtered);

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
     * Sets the current service api name to interact with it.
     *
     * @param  string  $service
     * @return void
     */
    public function setService($service)
    {
        $this->service = $service;
    }

}
