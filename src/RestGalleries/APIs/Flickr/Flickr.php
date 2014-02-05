<?php

namespace RestGalleries\APIs\Flickr;

use Guzzle\Http\Client;
use RestGalleries\APIs\Flickr\FlickrPhotos;
use RestGalleries\Cache\RestCache;
use RestGalleries\Interfaces\Gallery;

/**
 * An specific API client for interact with Flickr services.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class Flickr implements Gallery
{
    private $rest_url = 'http://api.flickr.com/services/rest/';

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
     * Searches and return all objects with the received values from the API service.
     *
     * @param    array            $api_key      API rest model value.
     * @param    string           $secret_key   API rest model value.
     * @param    string           $args         Array of arguments for HTTP API service request.
     *
     * @return   array/boolean                  Returns the galleries found, but returns false.
     */
    public function get($api_key, $secret_key, $args)
    {
        $client  = new Client($this->rest_url);

        $cache = new RestCache($client);
        $cache->setDevMode(true);
        $cache->make();

        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $api_key);
        $query->set('user_id', $args['user_id']);

        $query->set('method', 'flickr.photosets.getList');

        $query->set('page', 'null');
        $query->set('per_page', 'null');
        $query->set('primary_photo_extras', 'null');

        $response = $request->send();
        $body     = $response->getBody();
        $data     = json_decode($body->__toString());

        foreach ($data->photosets->photoset as $gallery) {
            $galleries[] = $this->getObject($api_key, $gallery);
        }

        return $galleries;

    }

    /**
     * Searches and return a single object with the received values from the API service.
     *
     * @param    string           $api_key      API rest model value.
     * @param    string           $secret_key   API rest model value.
     * @param    array            $args         Array of arguments for HTTP API service request.
     * @param    string/integer   $id           ID gallery number to search.
     *
     * @return   object/boolean                 Returns the gallery found, but returns false.
     */
    public function find($api_key, $secret_key, $args, $id)
    {
        $client  = new Client($this->rest_url);

        $cache = new RestCache($client);
        $cache->setDevMode(true);
        $cache->make();

        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $api_key);
        $query->set('user_id', $args['user_id']);

        $query->set('method', 'flickr.photosets.getList');

        $query->set('page', 'null');
        $query->set('per_page', 'null');
        $query->set('primary_photo_extras', 'null');

        $response = $request->send();
        $body     = $response->getBody();
        $data     = json_decode($body->__toString());

        foreach ($data->photosets->photoset as $gallery) {
            if ($gallery->id == $id) {
                return $this->getObject($api_key, $gallery);
            }
        }

        return false;

    }

    /**
     * Sets and returns an instance with the new values from raw data object given.
     *
     * @param    array            $api_key   API rest model value.
     * @param    object           $gallery   Raw object data to use.
     *
     * @return   object                      Returns an instance of this object with the properties set.
     */
    private function getObject($api_key, $gallery)
    {
        $instance              = new self;
        $photos                = new FlickrPhotos;

        $instance->id          = $gallery->id;
        $instance->title       = $gallery->title->_content;
        $instance->description = $gallery->description->_content;
        //$instance->$url      = 'http://...';
        $instance->published   = date('Y-m-d H:i:s', $gallery->date_create);
        $instance->photos      = $photos->get($api_key, $gallery->id);
        //$instance->category  = $gallery->category;
        //$instance->keywords  = $gallery->keywords;
        $instance->thumbnail   = 'http://farm' . $gallery->farm . '.staticflickr.com/' . $gallery->server . '/' . $gallery->primary . '_' . $gallery->secret . '.jpg';
        $instance->size        = $gallery->photos + $gallery->videos;

        return $instance;

    }

}

