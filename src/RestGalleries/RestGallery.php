<?php namespace RestGalleries;

use RestGalleries\Factory;

abstract class RestGallery
{
    protected $service;

    protected $query;

    protected $credentials = [];

    public function all()
    {
        return $this->query->all();
    }

    public function find($id)
    {
        return $this->query->find($id);
    }

    public static function authenticate(array $tokenCredentials)
    {
        $instance        = new static;
        $query           = $instance->newQuery();
        $instance->query = &$query;

        $instance->setCredentials($tokenCredentials);
        $query->setAuth($instance->getCredentials());

        return $instance;

    }

    public function newQuery()
    {
        $class = $this->getService() . '\\Gallery';

        return Factory::make($class);

    }

    public static function connect(array $clientCredentials)
    {
        $instance = new static;
        $auth     = $instance->newAuthentication();

        $instance->setCredentials($clientCredentials);

        return $auth->connect($instance->getCredentials());

    }

    public static function verifyCredentials(array $tokenCredentials)
    {
        $instance = new static;
        $auth     = $instance->newAuthentication();

        $instance->setCredentials($tokenCredentials);

        return $auth->verifyCredentials($instance->getCredentials());

    }

    public function newAuthentication()
    {
        $class = $this->getService() . '\\User';

        return Factory::make($class);

    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function setCredentials(array $credentials)
    {
        $this->credentials = $this->addCredentials($credentials);
        $this->credentials = $this->cleanCredentials();
    }

    private function addCredentials($credentials)
    {
        return array_merge($this->getCredentials(), $credentials);
    }

    private function cleanCredentials()
    {
        $filtered = array_filter($this->getCredentials());

        return array_unique_keys($filtered);

    }

    public function getService()
    {
        if (isset($this->service)) {
            return $this->service;
        }

        return class_basename($this);

    }

    public function setService($service)
    {
        $this->service = $service;
    }
}
