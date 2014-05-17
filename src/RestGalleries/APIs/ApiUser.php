<?php namespace RestGalleries\APIs;

use Illuminate\Support\Fluent;
use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Exception\AuthException;
use RestGalleries\Interfaces\UserAdapter;

/**
 * Makes the hard work for the user classes of the different APIs.
 */
abstract class ApiUser implements UserAdapter
{
    /**
     * Auth client.
     *
     * @var object
     */
    protected $auth;

    /**
     * Url where to get the account details..
     *
     * @var string
     */
    protected $checkUrl;

    /**
     * OAuth request endpoint.
     *
     * @var string
     */
    protected $urlRequest;

    /**
     * OAuth authorization endpoint.
     *
     * @var string
     */
    protected $urlAuthorize;

    /**
     * OAuth access endpoint.
     *
     * @var string
     */
    protected $urlAccess;

    public function __construct(AuthAdapter $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Makes all the OAuth process to connect the app with the API, only, with the client credentials, the oauth endpoints urls, and an URL to get the user data and token credentials.
     *
     * @param  array  $clientCredentials
     * @return object
     */
    public function connect(array $clientCredentials)
    {
        $endPoints = [
            'request'   => $this->urlRequest,
            'authorize' => $this->urlAuthorize,
            'access'    => $this->urlAccess,
        ];

        $auth = $this->auth;

        $data = $auth::connect($clientCredentials, array_filter($endPoints), $this->checkUrl);

        return $this->getUser($data);

    }

    /**
     * Checks the token credentials and returns an object with the data (and tokens) from the user account.
     * If for any reason the credentials are invalid, throws an exception.
     *
     * @param  array                                  $tokenCredentials
     * @return object
     */
    public function verifyCredentials(array $tokenCredentials)
    {
        $auth = $this->auth;

        $data = $auth::verifyCredentials($tokenCredentials, $this->checkUrl);

        return $this->getUser($data);

    }

    /**
     * Gets an array of account data and returns an object. If it not get user data throws an exception.
     *
     * @param  object $data
     * @throws RestGalleries\Exception\AuthException;
     * @return Illuminate\Support\Fluent
     */
    private function getUser($data)
    {
        $user = $this->getArrayData($data);

        if (!$user) {
            throw new AuthException('The authentication credentials are outdated or are not valid');
        }

        return new Fluent($user);

    }

    /**
     * Normalize request given data into an array.
     *
     * @param  object        $data
     * @return boolean|array
     */
    abstract protected function getArrayData($data);

}
