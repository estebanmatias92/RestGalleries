<?php //namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiClient;
use RestGalleries\Client\HttpClient;
use RestGalleries\Exception\RestGalleriesException;
use RestGalleries\Support\Traits\Overload;

/**
 * This class is responsible for search and return a specific user from the API service.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class User extends ApiClient
{
    use Overload;

    protected $endPoint = 'http://api.flickr.com/services/rest/';
    protected $client = null;

    public $id;
    public $url;
    public $realname;
    public $username;

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
     * Searchs and returns a specific user.
     *
     * @param    string           $username     Username for search the user.
     *
     * @return   object                         Returns the user when find him, but returns false.
     *
     * @throws   RestGalleries\Exception\RestGalleriesException
     */
    public function findUser($username)
    {
        $query = [
            'api_key'  => $this->apiKey,
            'username' => $username,
            'method'   => 'flickr.people.findByUsername',
        ];

        $this->client->setRequest();
        $this->client->setQuery($query);
        $this->client->sendRequest();

        $data = $this->client->getResponse();

        if (!isset($data->user)) {
            switch ($data->code) {
                case 1:
                    throw new RestGalleriesException('User not found');
                    break;
                case 100:
                    throw new RestGalleriesException('Invalid API Key');
                    break;
            }

            throw new \Exception($data->message);
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
        $query = [
            'api_key' => $this->apiKey,
            'user_id' => $id,
            'method'  => 'flickr.people.getInfo',
        ];

        $this->client->setRequest();
        $this->client->setQuery($query);
        $this->client->sendRequest();

        $data = $this->client->getResponse();

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
        $this->id       = $user->id;
        $this->url      = $user->profileurl->_content;
        $this->realname = $user->realname->_content;
        $this->username = $user->username->_content;

        return $this;

    }

}
