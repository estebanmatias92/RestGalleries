<?php

require('Flickr.php');

/**
 * Rest-model test.
 */
class FlickrTest extends PHPUnit_Framework_TestCase
{

    public function testFindUser()
    {
        $user = Flickr::findUser('estebanmatias092');

        $this->assertNotNull($user->realname);
    }

    public function testAll()
    {
        $args = [
            'user_id' => '96330205@N04',
        ];

        $galleries = Flickr::all($args);

        $this->assertNotNull($galleries[0]->title);
    }

    public function testFind()
    {
        $args = [
            'user_id' => '96330205@N04',
        ];

        $gallery = Flickr::find($args, '72157633780762609');

        $this->assertNotNull($gallery->title);
    }

}
