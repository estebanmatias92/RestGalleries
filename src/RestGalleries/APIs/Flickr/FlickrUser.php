<?php

namespace RestGalleries\APIs\Flickr;

use Guzzle\Http\Client;
use RestGalleries\Cache\RestCache;
use RestGalleries\Exception\RestGalleriesException;
use RestGalleries\Interfaces\User;

/**
 * This class is responsible for search and return a specific user from the API service.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class FlickrUser implements User
{
    private $rest_url = 'http://api.flickr.com/services/rest/';
    private $apiKey;
    private $secretKey;
    private $developmentMode;

    public $id;
    public $url;
    public $realname;

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
     * Searchs and returns a specific user.
     *
     * @param    string           $username     Username for search the user.
     *
     * @return   object                         Returns the user when find him, but returns false.
     *
     * @throws   RestGalleries\Exception\RestGalleriesException
     */
    public function findByUsername($username)
    {
        $client  = new Client($this->rest_url);

        $cache = new RestCache($client);
        $cache->setDevelopmentMode($this->developmentMode);
        $cache->make();

        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $this->apiKey);
        $query->set('username', $username);

        $query->set('method', 'flickr.people.findByUsername');

        $response = $request->send();
        $body     = $response->getBody();
        $data     = json_decode($body->__toString());

        if (!isset($data->user)) {
            switch ($data->code) {
                case 1:
                    throw new RestGalleriesException('User not found');
                    break;
                case 100:
                    throw new RestGalleriesException('Invalid API Key');
                    break;
            }
        }

        return $this->get($data->user->nsid);

    }

    /**
     * Gets the user data from its ID.
     *
     * @param    string           $id        ser ID for search data.
     *
     * @return   object                      Raw data object.
     */
    public function get($id)
    {
        $client  = new Client($this->rest_url);

        $cache = new RestCache($client);
        $cache->setDevelopmentMode($this->developmentMode);
        $cache->make();

        $request = $client->get();
        $query   = $request->getQuery();

        $query->set('format', 'json');
        $query->set('nojsoncallback', 1);
        $query->set('api_key', $this->apiKey);
        $query->set('user_id', $id);

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
