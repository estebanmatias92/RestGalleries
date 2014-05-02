<?php namespace RestGalleries;

use RestGalleries\Factory;
use RestGalleries\Interfaces\Api;

abstract class RestGallery
{
    private $api;

    protected $apiKey;

    protected $secretKey;

    public function __construct(Api $api = null)
    {
        $this->api = isset($api) ? $api : Factory::make(get_class($this));
    }

    public function setAccount($username, $password)
    {
        $data = [
            'username'  => $username,
            'password'  => $password,
            'apiKey'    => $this->apiKey,
            'secretKey' => $this->secretKey,
        ];

        $this->api->setAccount($data);
    }

    public function all()
    {
        return $this->api->all();
    }

    public function find($id)
    {
        return $this->api->find($id);
    }

    public function findUser($username)
    {
        return $this->api->findUser($username);
    }

}
