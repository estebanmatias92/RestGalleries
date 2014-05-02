<?php

class FlickrTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gallery = Mockery::mock('RestGalleries\\APIs\\Flickr\Gallery');

        $this->user = Mockery::mock('RestGalleries\\APIs\\Flickr\User');

        $this->api = new Flickr($this->gallery, $this->user);

    }

    public function testAllReturnGalleriesArray()
    {
        $this->gallery
            ->shouldReceive('all')
            ->andReturn([Mockery::mock('RestGalleries\\APIs\\Flickr\\Gallery')]);

        $galleries = $this->api->all();

        $this->assertInternalType('array', $galleries);
        $this->assertInstanceOf('RestGalleries\\APIs\\Flickr\\Gallery', $galleries[0]);

    }

    public function testFind()
    {
        $galleryId = '1234galleryId';

        $this->gallery
            ->shouldReceive('find')
            ->with($galleryId)
            ->andReturn($galleryMock = Mockery::mock('RestGalleries\\APIs\\Flickr\\Gallery'));

        $galleryMock->id = $galleryId;

        $gallery = $this->api->find($galleryId);

        $this->assertEquals($galleryId, $gallery->id);

    }

    public function testFindUser()
    {
        $username = 'username';

        $this->user
            ->shouldReceive('findUser')
            ->with($username)
            ->andReturn($userMock = Mockery::mock('RestGalleries\\APIs\\Flickr\\User'));

        $userMock->username = $username;

        $user = $this->api->findUser($username);

        $this->assertEquals($username, $user->username);
    }
}
