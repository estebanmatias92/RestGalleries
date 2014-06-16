<?php



class RestGalleryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->galleryService  = Mockery::mock('RestGalleries\\APIs\\Flickr\\Gallery');

        $this->auth = Mockery::mock('RestGalleries\\Auth\\OhmyAuth\\OhmyAuth');

        $this->restGallery = Mockery::mock(
            'RestGalleries\\RestGallery',
            [$this->auth, $this->galleryService]
            )->makePartial();

    }

    public function testAll()
    {
        $this->galleryService
            ->shouldReceive('all')
            ->andReturn(new Illuminate\Support\Collection(
                [
                    new Illuminate\Support\Fluent,
                    new Illuminate\Support\Fluent
                ]
            ));

        $galleries = $this->restGallery->all();

        assertThat($galleries, is(nonEmptyTraversable()));
        assertThat((array) $galleries, everyItem(hasValue(anObject())));

    }

    public function testFind()
    {
        $id = '123456789123456';

        $this->galleryService
            ->shouldReceive('find')
            ->with($id)
            ->andReturn(new Illuminate\Support\Fluent);

        $gallery = $this->restGallery->find($id);

        assertThat($gallery, is(objectValue()));

    }

    public function testAuthenticate()
    {
        $apiKeys = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret'
        ];

        $credentials = [
            'consumer_key'    => 'dummy-consumer-key',
            'consumer_secret' => 'dummy-consumer-secret',
            'token'           => 'dummy-token',
            'token_secret'    => 'dummy-token-secret'
        ];

        $this->galleryService
            ->shouldReceive('setAuth')
            ->with($credentials);


        $this->restGallery->authenticate($credentials);
    }

}
