<?php namespace RestGalleries\APIs;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Interfaces\GalleryAdapter;
use RestGalleries\Interfaces\PhotoAdapter;

/**
 * Normalizes all <API>\Gallery classes under one single interface, and simplifies the reuse of public methods.
 */
abstract class ApiGallery implements GalleryAdapter
{
    /**
     * Url to the API REST. the base for all requests.
     *
     * @var string
     */
    protected $endPoint;

    /**
     * Auth client to connect with the service and verify the credentials.
     *
     * @var object
     */
    protected $auth;

    /**
     * HTTP client to make the requests to the API.
     *
     * @var object
     */
    protected $http;

    /**
     * Stores the photo object.
     *
     * @var object
     */
    protected $photo;

    /**
     * Initializes instance variables.
     *
     * @param  \RestGalleries\Auth\AuthAdapter        $auth
     * @param  \RestGalleries\Http\HttpAdapter        $http
     * @param  \RestGalleries\Interfaces\PhotoAdapter $photo
     * @return void
     */
    public function __construct(AuthAdapter $auth, HttpAdapter $http, PhotoAdapter $photo)
    {
        $this->auth  = $auth;
        $this->http  = $http::init($this->endPoint);
        $this->photo = $photo;

    }

    /**
     * Returns all galleries currently available on the photos service.
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function all()
    {
        return $this->getGalleries();
    }

    /**
     * Fetch gallery ids, and iterate them to get every gallery from its id.
     * Returns an ArrayObject-type with all new galleries obtained,
     *
     * @return \Illuminate\Support\Collection|null
     */
    protected function getGalleries()
    {
        if (! is_null($ids = $this->fetchIds())) {
            $galleries = array_map([$this, 'getGallery'], $ids);
            return new Collection($galleries);
        }

    }

    /**
     * Makes the request to get all current ids of galleries as an array and returns them.
     *
     * @return array|null
     */
    abstract protected function fetchIds();

    /**
     * Fetch a gallery as array and returns a object ArrayAccess-type with that data.
     *
     * @param  string $id
     * @return \Illuminate\Support\Fluent|null
     */
    protected function getGallery($id)
    {
        if (! is_null($gallery = $this->fetchGallery($id))) {
            return new Fluent($gallery);
        }

    }

    /**
     * This function makes the request to get an particular gallery and returns its data as an array.
     *
     * @param  string $id
     * @return array|null
     */
    abstract protected function fetchGallery($id);

    /**
     * Returns a particular gallery.
     *
     * @param  string $id
     * @return \Illuminate\Support\Fluent|null
     */

    public function find($id)
    {
        return $this->getGallery($id);
    }

    /**
     * Set tokens for authentication.
     *
     * @param  array $tokenCredentials
     * @return void
     */
    public function setAuth(array $tokenCredentials)
    {
        $this->http->setAuth($tokenCredentials);
    }

    /**
     * Set cache file system and path, for caching.
     *
     * @param  string $fileSystem
     * @param  array  $path
     * @return void
     */
    public function setCache($fileSystem, array $path)
    {
        $this->http->setCache($fileSystem, $path);
    }

}
