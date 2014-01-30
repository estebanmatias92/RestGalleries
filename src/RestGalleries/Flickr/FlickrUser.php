<?php

namespace RestGalleries\Flickr;

use RestGalleries\interfaces\ApiUser;
use Guzzle\Http\Client;

/**
 * This class is responsible for search and return a specific user from the API service.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class FlickrUser implements ApiUser
{
    private $rest_url = 'http://api.flickr.com/services/rest/';

    public $id;
    public $url;
    public $realname;

    /**
     * Searchs and returns a specific user.
     *
     * @param    array            $args       Arguments to use in the HTTP request.
     * @param    string           $username   Username for search the user.
     *
     * @return   object                       Returns the user when find him, else returns false.
     */
    public function findByUsername($args, $username)
    {
        $client  = new Client($this->rest_url);
        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $args['api_key']);
        $query->set('username', $username);

        $query->set('method', 'flickr.people.findByUsername');

        $response = $request->send();
        $body     = $response->getBody();
        $data     = json_decode($body->__toString());

        if (null === $data->user->nsid) {
            return false;
        }

        return $this->get($args, $data->user->nsid);

    }

    /**
     * Gets the user data from its ID.
     *
     * @param    array            $args   Arguments to use in the HTTP request.
     * @param    string           $nsid   User ID for search data.
     *
     * @return   object                   Raw data object.
     */
    public function get($args, $nsid)
    {
        $client  = new Client($this->rest_url);
        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $args['api_key']);
        $query->set('user_id', $nsid);

        $query->set('method', 'flickr.people.getInfo');

        $response = $request->send();
        $body     = $response->getBody();
        $data     = json_decode($body->__toString());

        return $this->getObject($data->person);

    }

    /**
     * Sets and returns an instance with the new values from raw data object given.
     *
     * @param    object           $user   Raw object data to use.
     *
     * @return   object                   An instance with the object values.
     */
    private function getObject($user)
    {
        $instance           = new self;

        $instance->id       = $user->id;
        $instance->url      = $user->profileurl->_content;
        $instance->realname = $user->realname->_content;

        return $instance;

    }

}
