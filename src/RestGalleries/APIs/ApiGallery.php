<?php namespace RestGalleries\APIs;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Interfaces\GalleryAdapter;
use RestGalleries\Interfaces\PhotoAdapter;

/**
 * ApiGallery description.
 */
abstract class ApiGallery implements GalleryAdapter
{
    protected $endPoint;

    protected $auth;

    protected $http;

    protected $photo;

    public function __construct(AuthAdapter $auth, HttpAdapter $http, PhotoAdapter $photo)
    {
        $this->auth  = $auth;
        $this->http  = $http::init($this->endPoint);
        $this->photo = $photo;

    }

    public function all()
    {
        return $this->getGalleries();
    }

    protected function getGalleries()
    {
        if (! is_null($ids = $this->fetchIds())) {
            $galleries = array_map([$this, 'getGallery'], $ids);
            return new Collection($galleries);
        }

    }

    abstract protected function fetchIds();

    protected function getGallery($id)
    {
        if (! is_null($gallery = $this->fetchGallery($id))) {
            return new Fluent($gallery);
        }

    }

    abstract protected function fetchGallery($id);

    public function find($id)
    {
        return $this->getGallery($id);
    }

    /**
     * Set tokens for authentication.
     *
     * @param array $tokenCredentials
     */
    public function setAuth(array $tokenCredentials)
    {
        $this->http->setAuth($tokenCredentials);
    }

    /**
     * Set cache file system and path, for caching.
     *
     * @param string $fileSystem
     * @param array  $path
     */
    public function setCache($fileSystem, array $path)
    {
        $this->http->setCache($fileSystem, $path);
    }

}
