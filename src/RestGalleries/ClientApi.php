<?php

namespace RestGalleries;

use RestGalleries\interfaces\ApiGallery;
use RestGalleries\interfaces\ApiUser;

/**
 * Serves as facade to interact with multiple APIs.
 * Uses dependecy injection for load the API to use.
 */
class ClientApi
{
    /**
     * Orders search to a particular api an user by his username.
     *
     * @param    ApiUser          $client       API to interact with.
     * @param    string           $api_key      API rest model value.
     * @param    string           $secret_key   API rest model value.
     * @param    array            $username     Username for search the user.
     *
     * @return   bject/boolean                  Returns the user when find him, else returns false.
     */
    public function findUser(ApiUser $client, $api_key, $secret_key, $username)
    {
        return $client->findByUsername($api_key, $secret_key, $username);
    }

    /**
     * Orders search to a particular api all existing galleries for a particular user.
     *
     * @param    ApiGallery       $client       API to interact with.
     * @param    string           $api_key      API rest model value.
     * @param    string           $secret_key   API rest model value.
     * @param    array            $args         Array of arguments to pass to the API (like, user_id, password, etc).
     *
     * @return   object/boolean                 Returns the galleries found, else returns false.
     */
    public function get(ApiGallery $client, $api_key, $secret_key, $args)
    {
        return $client->get($api_key, $secret_key, $args);
    }

    /**
     * Orders search to a particular api a particular gallery for a particular user.
     *
     * @param    ApiGallery       $client       API to interact with.
     * @param    string           $api_key      API rest model value.
     * @param    string           $secret_key   API rest model value.
     * @param    array            $args         Array of arguments to pass to the API (like, user_id, password, etc).
     * @param    string/integer   $id           ID gallery number to search.
     *
     * @return   object/boolean                 Returns the gallery found, else returns false.
     */
    public function find(ApiGallery $client, $api_key, $secret_key, $args, $id)
    {
        return $client->find($api_key, $secret_key, $args, $id);
    }
}
