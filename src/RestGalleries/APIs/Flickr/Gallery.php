<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiClient;
use RestGalleries\APIs\Flickr\Photo;
use RestGalleries\APIs\Flickr\Account;
use RestGalleries\Client\HttpClient;
use RestGalleries\Exception\RestGalleriesException;
use RestGalleries\Support\Traits\Overload;

/**
 * An specific API client for interact with Flickr services.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class Gallery extends ApiClient implements ApiGallery
{
    use Overload;

    protected $endPoint = 'http://api.flickr.com/services/rest/';
    protected $client = null;
    protected $photo;
    protected $user;

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
     */
    public function __construct(array $attributes = array())
    {
        $this->photo = new Photo;
        $this->user  = new Account;

        $this->setAttributes($attributes);

        $this->client = $this->newClient();
    }

    public function setAccount($data)
    {
        parent::setAccount($data);

        $this->photo->setAccount($data);
        $this->user->setAccount($data);

    }

    protected function newClient()
    {
        $options  = [
            'query' => [
                'format' => 'json',
                'nojsoncallback' => 1,
            ],
        ];

        return new HttpClient($this->endPoint, $options);

    }

    public function all()
    {
        $query = [
            'api_key'              => $this->apiKey,
            'user_id'              => $this->userId,
            'method'               => 'flickr.photosets.getList',
            'page'                 => 'null',
            'per_page'             => 'null',
            'prymary_photo_extras' => 'null',
        ];

        $this->client->setRequest();
        $this->client->setQuery($query);
        $this->client->sendRequest();

        $data = $this->client->getResponse();

        if (!isset($data->photosets))
        {
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
    public function find($args = null, $id = null)
    {
        $client = new Client($this->endPoint);

        $cache = new RestCache($client);
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

        if (!isset($data->photosets->photoset))
        {
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
        $instance = new static($this->getAttributes());

        $instance->id          = $gallery->id;
        $instance->title       = $gallery->title->_content;
        $instance->description = $gallery->description->_content;
        //$instance->$url      = 'http://...';
        $instance->published   = date('Y-m-d H:i:s', $gallery->date_create);
        $instance->photos      = $this->photo->get($gallery->id);
        //$instance->category  = $gallery->category;
        //$instance->keywords  = $gallery->keywords;
        $instance->thumbnail   = 'http://farm' . $gallery->farm . '.staticflickr.com/' . $gallery->server . '/' . $gallery->primary . '_' . $gallery->secret . '.jpg';
        $instance->size        = $gallery->photos + $gallery->videos;

        return $instance;

    }

}

