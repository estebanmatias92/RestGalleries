<?php

namespace RestGalleries;

use RestGalleries\interfaces\Gallery;
use RestGalleries\interfaces\User;

/**
 * Serves as facade to interact with multiple APIs.
 * Uses dependecy injection for load the API to use.
 */
class ClientApi
{
    /**
     * Orders search to a particular api an user by his username.
     *
     * @param    User             $api          API to interact with.
     * @param    array            $username     Username for search the user.
     *
     * @return   object/boolean                 Returns the user when find him, else returns false.
     */
    public function findUser(User $api, $username)
    {
        return $api->findByUsername($username);
    }

    /**
     * Orders search to a particular api all existing galleries for a particular user.
     *
     * @param    Gallery          $api          API to interact with.
     * @param    array            $args         Array of arguments to pass to the API (like, user_id, password, etc).
     *
     * @return   object/boolean                 Returns the galleries found, else returns an empty array.
     */
    public function get(Gallery $api, $args)
    {
        return $api->get($args);
    }

    /**
     * Orders search to a particular api a particular gallery for a particular user.
     *
     * @param    Gallery          $api          API to interact with.
     * @param    array            $args         Array of arguments to pass to the API (like, user_id, password, etc).
     * @param    string/integer   $id           ID gallery number to search.
     *
     * @return   object/boolean                 Returns the gallery found, else returns false.
     */
    public function find(Gallery $api, $args, $id)
    {
        return $api->find($args, $id);
    }
}
