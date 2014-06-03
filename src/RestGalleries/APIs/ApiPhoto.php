<?php namespace RestGalleries\APIs;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use RestGalleries\Http\HttpAdapter;

/**
 * ApiPhoto description.
 */
class ApiPhoto
{
    protected $endPoint;
    protected $http;

    public function __construct(HttpAdapter $http)
    {
        $this->http = $http::init($this->endPoint);
    }

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

    public function find($id)
    {
        $photo = $this->getPhoto($id);

        return new Fluent($photo);

    }

    public function setAuth(array $tokenCredentials)
    {
        $this->http->setAuth($tokenCredentials);
    }

    public function setCache($fileSystem, array $path)
    {
        $this->http->setCache($fileSystem, $path);
    }

}
