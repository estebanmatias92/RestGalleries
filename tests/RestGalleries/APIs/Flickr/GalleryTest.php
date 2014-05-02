<?php

use RestGalleries\APIs\Flickr\Gallery;

class GalleryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gallery = new Gallery();
        $this->gallery->setAccount([
            'username'   => 'flickr',
            'password'   => '1234',
            'api_key'    => '9fc87a455f82c80f42b3c707da70fc77',
            'secret_key' => '',
        ]);

    }

    public function testAll()
    {
        $galleries = $this->gallery->all();

        $this->assertInternalType('array', $galleries);
        $this->assertInstanceOf('RestGalleries\\APIs\\Flickr\\Gallery', $galleries[0]);

    }

}
