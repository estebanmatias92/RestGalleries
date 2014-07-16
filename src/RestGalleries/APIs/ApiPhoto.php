<?php namespace RestGalleries\APIs;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use RestGalleries\Http\Guzzle\GuzzleRequest;
use RestGalleries\Http\Guzzle\Plugins\GuzzleAuth;
use RestGalleries\Http\Guzzle\Plugins\GuzzleCache;
use RestGalleries\Http\RequestAdapter;
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

    protected $plugins = [];

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

    public function addPlugin($plugin)
    {
        $this->plugins[] = $plugin;
    }

}
