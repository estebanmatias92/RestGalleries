<?php namespace RestGalleries\APIs;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use RestGalleries\Http\RequestAdapter;
use RestGalleries\Http\Guzzle\GuzzleRequest;
use RestGalleries\Http\Guzzle\Plugins\GuzzleAuth;
use RestGalleries\Http\Guzzle\Plugins\GuzzleCache;
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

    protected $cache = [];

    protected $credentials = [];

    protected $plugins = [];

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

    public function newRequest(RequestAdapter $request = null)
    {
        if (empty($request)) {
            $request = new GuzzleRequest;
        }

        $request = $request::init($this->endPoint);

        if (! empty($this->plugins)) {
            $request = $request->addPlugins($this->plugins);
        }

        return $request;

    }

    protected function newRequestAuthPlugin()
    {
        return new GuzzleAuth;
    }

    protected function newRequestCachePlugin()
    {
        return new GuzzleCache;
    }

    public function newPhoto(PhotoAdapter $photo = null)
    {
        if (empty($photo)) {
            $class = $this->getChildClassNamespace() . '\\Photo';
            $photo = new $class;
        }

        $photo->addAuthentication($this->credentials);

        if (! empty($this->cache)) {
            $photo->addCache(
                $this->cache['system'],
                $this->cache['path']
            );
        }

        return $photo;

    }

    protected function getChildClassNamespace()
    {
        return get_class_namespace($this);
    }

    public function addAuthentication(array $credentials)
    {
        $plugin = $this->newRequestAuthPlugin();
        $this->plugins['auth'] = $plugin::add($credentials);
        $this->credentials     = $credentials;
    }

    public function addCache($system, array $path)
    {
        $plugin = $this->newRequestCachePlugin();
        $this->plugins['cache'] = $plugin::add($system, $path);
        $this->cache['system']  = $system;
        $this->cache['path']    = $path;
    }

}
