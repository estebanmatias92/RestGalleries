<?php



class RestGalleryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->api = Mockery::mock('Flickr');

        $this->mock = Mockery::mock('RestGalleries\\RestGallery', [$this->api])->makePartial();

    }

    public function testAllReturnArray()
    {
        $this->api
            ->shouldReceive('all')
            ->andReturn(array());

        $galleries = $this->mock->all();

        $this->assertInternalType('array', $galleries);

    }

    public function testAllReturnCorrectInstances()
    {
        $this->api
            ->shouldReceive('all')
            ->andReturn([Mockery::mock('RestGalleries\\Apis\\Flickr\\Gallery')]);

        $galleries = $this->mock->all();

        $this->assertInstanceOf('RestGalleries\\APIs\\Flickr\\Gallery', $galleries[0]);

    }

    public function testFind()
    {
        $galleryId = '12354galleryId';

        $this->api
            ->shouldReceive('find')
            ->with($galleryId)
            ->andReturn($galleryMock =Mockery::mock('RestGalleries\\Apis\\Flickr\\Gallery'));

        $galleryMock->id = $galleryId;

        $gallery = $this->mock->find($galleryId);

        $this->assertEquals($galleryId, $gallery->id);
    }

    public function testFindUser()
    {
        $username = 'username';

        $this->api
            ->shouldReceive('findUser')
            ->with($username)
            ->andReturn($userMock = Mockery::mock('RestGalleries\\APIs\\Flickr\\User'));

        $userMock->username = $username;

        $user = $this->mock->findUser($username);

        $this->assertEquals($username, $user->username);
    }

}
