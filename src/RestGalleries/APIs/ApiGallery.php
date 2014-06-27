<?php namespace RestGalleries\APIs;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Http\Guzzle\GuzzleHttp;
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

    protected $credentials = [];

    protected $cache  = [];

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

    public function newHttp(HttpAdapter $http = null)
    {
        if (empty($http)) {
            $http = new GuzzleHttp;
        }

        $http = $http::init($this->endPoint);
        $http->setAuth($this->getCredentials());
        $cache = $this->getCache();
        $http->setCache($cache['file_system'], $cache['path']);

        return $http;

    }

    public function newPhoto(PhotoAdapter $photo = null)
    {
        if (empty($photo)) {
            $namespace = $this->getChildClassNamespace();
            $class     = $namespace . '\\Photo';
            $photo     = new $class($this->newHttp());
        }

        return $photo;

    }

    protected function getChildClassNamespace()
    {
        return addslashes(get_class_namespace($this));
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
