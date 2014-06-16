<?php namespace RestGalleries;

use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Factory;
use RestGalleries\Interfaces\GalleryAdapter;

abstract class RestGallery
{
    protected $service;

    protected $gallery;

    protected $credentials;

    public function __construct(AuthAdapter $auth, GalleryAdapter $gallery = null)
    {
        static::$clientCredentialKeys = $auth::getClientCredentialKeys();
        static::$tokenCredentialKeys  = $auth::getAccessCredentialKeys();

        if (empty($gallery)) {
            $gallery = Factory::make(get_class($this));
        }

        $this->auth    = $auth;
        $this->gallery = $gallery;

    }

    public function all()
    {
        return $this->gallery->all();
    }

    public function find($id)
    {
        return $this->gallery->find($id);
    }

    public function authenticate(array $tokenCredentials)
    {
        $this->setTokenCredentials($tokenCredentials);
        $this->gallery->setAuth($this->credentials);
    }

    public static function connect(array $clientCredentials)
    {
        $this->setClientCredentials($clienCredentials);

        return $this->auth->connect($this->credentials);

    }

    public static function verifyCredentials(array $tokenCredentials)
    {
        $this->setTokenCredentials($tokenCredentials);

        return $this->auth->verifyCredentials($this->credentials);

    }

    protected function setClientCredentials($credentials)
    {
        $this->addToCredentials($credentials);
        $this->filterCredentials(static::$clientCredentialKeys);
    }

    protected function setTokenCredentials($credentials)
    {
        $this->addToCredentials($credentials);
        $this->filterCredentials(static::$tokenCredentialKeys);
    }

    private function addToCredentials($credentials)
    {
        $this->credentials = array_merge($this->credentials, $credentials);
    }

    private function filterCredentials(array $keys)
    {
        $credentials       = array_filter($this->credentials);
        $this->credentials = array_only($credentials, $keys);
    }

}
