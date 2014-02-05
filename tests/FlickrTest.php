<?php

require('Flickr.php');

/**
 * Rest-model test.
 */
class FlickrTest extends PHPUnit_Framework_TestCase
{

    public function testFindUser()
    {
        $user = Flickr::findUser('flickr');

        $this->assertNotNull($user->realname);
    }

    public function testAll()
    {
        $args = [
            'user_id' => '66956608@N06',
        ];

        $galleries = Flickr::all($args);

        $this->assertNotNull($galleries[0]->title);
    }

    public function testFind()
    {
        $args = [
            'user_id' => '66956608@N06',
        ];

        $gallery = Flickr::find($args, '72157639990929493');

        $this->assertNotNull($gallery->title);
    }

}
