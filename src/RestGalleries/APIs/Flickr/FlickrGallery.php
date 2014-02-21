<?php

namespace RestGalleries\APIs\Flickr;

use Guzzle\Http\Client;
use RestGalleries\APIs\Flickr\FlickrPhoto;
use RestGalleries\Cache\RestCache;
use RestGalleries\Exception\RestGalleriesException;
use RestGalleries\interfaces\Gallery;

/**
 * An specific API client for interact with Flickr services.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class FlickrGallery implements Gallery
{
    private $restUrl = 'http://api.flickr.com/services/rest/';
    private $apiKey;
    private $secretKey;
    private $developmentMode;

    public $id;
    public $title;
    public $description;
    public $url;
    public $published;
    public $photos;
    public $category;
    public $keywords;
    public $thumbnail;
    public $size;

    /**
     * @param   string           $apiKey            API rest model value.
     * @param   string           $secretKey         API rest model value.
     * @param   boolean          $developmentMode   [description]
     */
    public function __construct($apiKey = null, $secretKey = null, $developmentMode = false)
    {
        $this->apiKey          = $apiKey;
        $this->secretKey       = $secretKey;
        $this->developmentMode = $developmentMode;
    }

    /**
     * Searches and return all objects with the received values from the API service.
     *
     * @param    string           $args         Array of arguments for HTTP API service request.
     *
     * @return   array/boolean                  Returns the galleries found, but returns false.
     *
     * @throws   RestGalleries\Exception\RestGalleriesException
     */
    public function get($args)
    {
        $client  = new Client($this->restUrl);

        $cache = new RestCache($client);
        $cache->setDevelopmentMode($this->developmentMode);
        $cache->make();

        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $this->apiKey);
        $query->set('user_id', $args['user_id']);

        $query->set('method', 'flickr.photosets.getList');

        $query->set('page', 'null');
        $query->set('per_page', 'null');
        $query->set('primary_photo_extras', 'null');

        $response = $request->send();
        $body     = $response->getBody();
        $data     = json_decode($body->__toString());

        if (!isset($data->photosets)) {
            switch ($data->code) {
                case 1:
                    throw new RestGalleriesException('Galleries not found');
                    break;
                case 100:
                    throw new RestGalleriesException('Invalid API Key');
                    break;
            }
        }

        foreach ($data->photosets->photoset as $gallery) {
            $galleries[] = $this->getObject($gallery);
        }

        return $galleries;

    }

    /**
     * Searches and return a single object with the received values from the API service.
     *
     * @param    array            $args         Array of arguments for HTTP API service request.
     * @param    string/integer   $id           ID gallery number to search.
     *
     * @return   object/boolean                 Returns the gallery found, but returns false.
     *
     * @throws   RestGalleries\Exception\RestGalleriesException
     */
    public function find($args, $id)
    {
        $client  = new Client($this->restUrl);

        $cache = new RestCache($client);
        $cache->setDevelopmentMode($this->developmentMode);
        $cache->make();

        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $this->apiKey);
        $query->set('user_id', $args['user_id']);

        $query->set('method', 'flickr.photosets.getList');

        $query->set('page', 'null');
        $query->set('per_page', 'null');
        $query->set('primary_photo_extras', 'null');

        $response = $request->send();
        $body     = $response->getBody();
        $data     = json_decode($body->__toString());

        if (!isset($data->photosets->photoset)) {
            switch ($data->code) {
                case 1:
                    throw new RestGalleriesException('Gallery not found');
                    break;
                case 100:
                    throw new RestGalleriesException('Invalid API Key');
                    break;
            }
        }

        foreach ($data->photosets->photoset as $gallery) {
            if ($gallery->id == $id) {
                return $this->getObject($gallery);
            }
        }

        throw new RestGalleriesException('Gallery not found');

    }

    /**
     * Sets and returns an instance with the new values from raw data object given.
     *
     * @param    object           $gallery   Raw object data to use.
     *
     * @return   object                      Returns an instance of this object with the properties set.
     */
    private function getObject($gallery)
    {
        $instance              = new self;
        $photo                 = new FlickrPhoto($this->apiKey, $this->developmentMode);

        $instance->id          = $gallery->id;
        $instance->title       = $gallery->title->_content;
        $instance->description = $gallery->description->_content;
        //$instance->$url      = 'http://...';
        $instance->published   = date('Y-m-d H:i:s', $gallery->date_create);
        $instance->photos      = $photo->get($gallery->id);
        //$instance->category  = $gallery->category;
        //$instance->keywords  = $gallery->keywords;
        $instance->thumbnail   = 'http://farm' . $gallery->farm . '.staticflickr.com/' . $gallery->server . '/' . $gallery->primary . '_' . $gallery->secret . '.jpg';
        $instance->size        = $gallery->photos + $gallery->videos;

        return $instance;

    }

}

