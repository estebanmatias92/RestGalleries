<?php namespace RestGalleries\APIs;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
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

    /**
     * HTTP client to make the requests to the API.
     *
     * @var object
     */
    protected $http;

    public function __construct(HttpAdapter $http)
    {
        $this->http = $http::init($this->endPoint);
    }

    /**
     * Gets IDs of photos, seeks and gets the photo and makes a Collection for send it back.
     *
     * @param  string                        $galleryId
     * @return Illuminate\Support\Collection
     */
    public function all($galleryId)
    {
        $photoIds = $this->getPhotoIds($galleryId);
        $photos   = [];

        foreach ($photoIds as $id) {
            $photo    = $this->getPhoto($id);
            $photos[] = new Fluent($photo);
        }

        return new Collection($photos);

    }

    /**
     * Makes the request to get the IDs of gallery photos and return it as array.
     *
     * @param  string $galleryId
     * @return array
     */
    abstract protected function getPhotoIds($galleryId);

    /**
     * Gets the photo and returns an object.
     *
     * @param  string                    $id
     * @return Illuminate\Support\Fluent
     */
    public function find($id)
    {
        $photo = $this->getPhoto($id);

        return new Fluent($photo);

    }

    /**
     * Here are made the necessary requests to get info of photography, turn it  into an array, and send it back.
     *
     * @param  string $id
     * @return array
     */
    abstract protected function getPhoto($id);

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
