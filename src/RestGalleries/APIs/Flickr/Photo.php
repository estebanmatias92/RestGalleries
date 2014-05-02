<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiClient;
use RestGalleries\Client\HttpClient;
use RestGalleries\Exception\RestGalleriesException;
use RestGalleries\Support\Traits\Overload;

/**
 * This class is responsible for bringing the photos to a specific gallery.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class Photo extends ApiClient
{
    use Overload;

    protected $endPoint = 'http://api.flickr.com/services/rest/';
    protected $client = null;

    public $id;
    public $title;
    public $description;
    public $photo;

    /**
     * @param   string           $apiKey            API rest model value.
     * @param   string           $secretKey         API rest model value.
     */
    public function __construct(array $attributes = array())
    {
        $this->setAttributes($attributes);

        $this->client = $this->newClient();
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

    /**
     * Gets all photo objects for a specific gallery.
     *
     * @param    string           $id        ID gallery for search its photos.
     *
     * @return   array/boolean               Returns all photo objects in an array.
     */
    public function get($id)
    {
        $query = [
            'api_key'  => $this->apiKey,
            'method'   => 'flickr.photosets.getPhotos',
            'photoset_id'    => $id,
            'extras'         => 'tags, url_o',
            'privacy_filter' => 1,
            'page'           => 'null',
            'per_page'       => 'null',
            'media'          => 'all',
        ];

        $this->client->setRequest();
        $this->client->setQuery($query);
        $this->client->sendRequest();

        $data = $this->client->getResponse();

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
        if (!isset($photos->url_o)) {
            throw new RestGalleriesException('The account (' . $this->username . ') does not have sufficient permissions to see the original images.');
        }

        $instance              = new static($this->getAttributes());

        $instance->id          = $photo->id;
        $instance->title       = $photo->title;
        //$instance->description = $photo->description;
        $instance->photo       = $photo->url_o; // I need permissions public permissions for this

        return $instance;

    }
}

