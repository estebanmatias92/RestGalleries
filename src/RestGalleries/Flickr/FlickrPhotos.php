<?php

namespace RestGalleries\Flickr;

use Guzzle\Http\Client;

/**
 * This class is responsible for bringing the photos to a specific gallery.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class FlickrPhotos
{
    private $rest_url = 'http://api.flickr.com/services/rest/';

    public $id;
    public $title;
    public $description;
    public $photo;

    /**
     * Gets all photo objects for a specific gallery.
     *
     * @param    array            $api_key   API rest model value.
     * @param    string           $id        ID gallery for search its photos.
     *
     * @return   array/boolean               Returns all photo objects in an array.
     */
    public function get($api_key, $id)
    {
        $client  = new Client($this->rest_url);
        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $api_key);

        $query->set('method', 'flickr.photosets.getPhotos');

        $query->set('photoset_id', $id);
        $query->set('extras', 'tags, url_o');
        $query->set('privacy_filter ', 1);
        $query->set('page', 'null');
        $query->set('per_page', 'null');
        $query->set('media', 'all');

        $response = $request->send();
        $body     = $response->getBody();
        $data     = json_decode($body->__toString());

        foreach ($data->photoset->photo as $photo) {
            $photos[] = $this->getObject($photo);
        }

        return $photos;

    }

    /**
     * Sets and returns an instance with the new values from raw data object given.
     *
     * @param    object           $photo   Raw data object.
     *
     * @return   object                    An object instance with the new values received.
     */
    private function getObject($photo)
    {
        $instance              = new self;

        $instance->id          = $photo->id;
        $instance->title       = $photo->title;
        //$instance->description = $photo->description;
        $instance->photo       = $photo->url_o;

        return $instance;

    }
}

