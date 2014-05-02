<?php RestGalleries\Auth;

use Guzzle\Http\Client;
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use RestGalleries\Exception\AuthException;
use ohmy\Auth;

/**
 *
 */
class OhmyAuth implements OAuthClient
{
    protected $client;

    public function connect(array $clientCredentials, array $endPoints)
    {
        $clientCredentials = array_change_key_case($clientCredentials);
        $endPoints         = array_change_key_case($endPoints);

        $this->client = Auth::init($clientCredentials);

        foreach ($endPoints as $method => $url) {
            call_user_func_array(array($this->client, $method), array($url));
        }

        return $this->getTokenCredentials();
    }

    /**
     * [getTokenCredentials description]
     *
     * @return [type] [description]
     */
    public function getTokenCredentials()
    {
        $this->client->finally(function($data) use(&$tokenCredentials) {
            $tokenCredentials = $data;
        });

        return $tokenCredentials;
    }

    /**
     * [verifyCredentials description]
     *
     * @param  array  $tokenCredentials [description]
     * @param  array  $endPoint         [description]
     * @return [type]                   [description]
     */
    public function verifyCredentials(array $tokenCredentials, array $endPoint)
    {
        return $data;

        throw new AuthException('End-point or credentials are invalid');
    }

}
