<?php

use RestGalleries\ClientApi;
use RestGalleries\Flickr\Flickr;
use RestGalleries\Flickr\FlickrUser;

/**
 * Test ClienApi facade.
 */
class ClientApiTest extends PHPUnit_Framework_TestCase
{
    public function testFindUser()
    {
        $client = new ClientApi;

        $args = [
            'api_key' => '733da5d76386b4cc73c93ea660c3433a',
            'secret_key' => '',
            'user_id' => '96330205@N04'
        ];

        $user = $client->findUser(new FlickrUser, $args, 'estebanmatias092');

        $this->assertNotNull($user->realname);
    }

    public function testAll()
    {
        $client = new ClientApi;

        $args = [
            'api_key' => '733da5d76386b4cc73c93ea660c3433a',
            'secret_key' => '',
            'user_id' => '96330205@N04'
        ];

        $galleries = $client->get(new Flickr, $args);

        $this->assertNotNull($galleries[0]->title);
    }

    public function testFind()
    {
        $client = new ClientApi;

        $args = [
            'api_key' => '733da5d76386b4cc73c93ea660c3433a',
            'secret_key' => '',
            'user_id' => '96330205@N04'
        ];

        $gallery = $client->find(new Flickr, $args, '72157633780762609');

        $this->assertNotNull($gallery->title);
    }
}
