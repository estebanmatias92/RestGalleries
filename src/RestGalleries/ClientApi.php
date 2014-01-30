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
     * @param    ApiUser          $client     API to interact with.
     * @param    array            $args       Arguments to pass to the API.
     * @param    string           $username   Username for search the user.
     *
     * @return   object/boolean               Returns the user when find him, else returns false.
     */
    public function findUser(ApiUser $client, $args, $username)
    {
        return $client->findByUsername($args, $username);
    }

    /**
     * Orders search to a particular api all existing galleries for a particular user.
     *
     * @param    ApiGallery       $client   API to interact with.
     * @param    array            $args     Arguments to pass to the API.
     *
     * @return   object/boolean             Returns the galleries found, else returns false.
     */
    public function get(ApiGallery $client, $args)
    {
        return $client->get($args);
    }

    /**
     * Orders search to a particular api a particular gallery for a particular user.
     *
     * @param    ApiGallery       $client   API to interact with.
     * @param    array            $args     Arguments to pass to the API.
     * @param    string           $id       ID gallery number to search.
     *
     * @return   object/boolean             Returns the gallery found, else returns false.
     */
    public function find(ApiGallery $client, $args, $id)
    {
        return $client->find($args, $id);
    }
}
