<?php namespace RestGalleries\APIs;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use RestGalleries\Http\Guzzle\GuzzleHttp;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Interfaces\PhotoAdapter;

/**
 * Normalizes all APIs photo classes under one single interface, and simplifies the reuse of public methods.
 */
abstract class ApiPhoto implements PhotoAdapter
{
    /**
     * Url to the API REST. the base for all requests.
     *
     * @var string
     */
    protected $endPoint;

    protected $credentials = [];

    protected $cache = [];

    /**
     * Gets IDs of photos, seeks and gets the photo and makes a Collection for send it back.
     * If the gallery is empty, returns an empty Collection.
     *
     * @param  string                        $galleryId
     * @return Illuminate\Support\Collection
     */
    public function all($galleryId)
    {
        return $this->getPhotos($galleryId);
    }

    /**
     *
     *
     * @return \Illuminate\Support\Collection|null
     */
    protected function getPhotos($galleryId)
    {
        if (! is_null($ids = $this->fetchIds($galleryId))) {
            $photos = array_map([$this, 'getPhoto'], $ids);

            return new Collection($photos);
        }

    }

    /**
     * Makes the request to get the IDs of gallery photos and return it as array.
     * If it does not find the gallery, returns null.
     *
     * @param  string     $galleryId
     * @return array|null
     */
    abstract protected function fetchIds($galleryId);

    /**
     * Gets the photo and returns an object.
     * If photo is empty, returns an empty Fluent object.
     *
     * @param  string                    $id
     * @return Illuminate\Support\Fluent
     */
    public function find($id)
    {
        return $this->getPhoto($id);
    }

    /**
     * Fetch a gallery as array and returns a object ArrayAccess-type with that data.
     *
     * @param  string $id
     * @return \Illuminate\Support\Fluent|null
     */
    protected function getPhoto($id)
    {
        if (! is_null($photo = $this->fetchPhoto($id))) {
            return new Fluent($photo);
        }
    }

    /**
     * Here are made the necessary requests to get info of photography, turn it into an array, and send it back.
     * If it does not find the photo, returns null.
     *
     * @param  string     $id
     * @return array|null
     */
    abstract protected function fetchPhoto($id);

    public function newHttp(HttpAdapter $http = null)
    {
        if (empty($http)) {
            $http = new GuzzleHttp;
        }

        $http = $http::init($this->endPoint);
        $http->setAuth($this->getCredentials());

        if ($cache = $this->getCache()) {
            $http->setCache($cache['file_system'], $cache['path']);
        }

        return $http;

    }

    public function setCredentials(array $credentials)
    {
        $this->credentials = $credentials;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function setCache($fileSystem, array $path)
    {
        $this->cache['file_system'] = $fileSystem;
        $this->cache['path']        = $path;
    }

    public function getCache()
    {
        return $this->cache;
    }

}
