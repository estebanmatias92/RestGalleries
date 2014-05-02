<?php namespace RestGalleries\APis;

use RestGalleries\Interfaces\ApiGallery;
use RestGalleries\Interfaces\ApiUser;

abstract class ApiTemplate
{
    protected $gallery;

    protected $user;

    public function setAccount(array $data)
    {
        $this->gallery->setAccount($data);
        $this->user->setAccount($data);
        $this->verifyCredentials();
    }

    public function all()
    {
        return $this->gallery->all();
    }

    public function find($id)
    {
        return $this->gallery->find($id);
    }

    public function findUser($id)
    {
        return $this->user->find($id);
    }

    public function connect($callbackUrl)
    {
        return $this->user->connect($callbackUrl);
    }

    public function disconnect()
    {
        return $this->user->disconnect();
    }

    public function verifyCredentials()
    {
        return $this->user->verifyCredentials();
    }
}
