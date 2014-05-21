<?php

use RestGalleries\APIs\Flickr\Photo;

class PhotoTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $endPoint = 'http://api.flickr.com/services/rest/';

        $tokenCredentials = [
            'consumer_key'    => $this->faker->md5,
            'consumer_secret' => $this->faker->sha1,
            'token'           => $this->faker->md5,
            'token_secret'    => $this->faker->sha1,
        ];

        $this->http     = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleHttp');
        $this->response = Mockery::mock('RestGalleries\\Http\\Response');
        $this->photo    = new Photo($this->http);

        $this->http
            ->shouldReceive('init')
            ->with($endPoint)
            ->times(1);

        $this->http
            ->shouldReceive('setAuth')
            ->with($tokenCredentials)
            ->times(1);

        $this->http
            ->shouldReceive('setCache')
            ->with('array', array())
            ->times(1);

        $this->photo->setAuth($tokenCredentials);
        $this->photo->setCache('array', array());

    }

    public function testAll()
    {
        $stringResponse = '{ "photoset": { "id": "72157633782247768", "primary": "8876434399", "owner": "96330205@N04", "ownername": "estebanmatias092", "photo": [{ "id": "8876434399", "secret": "ba75f7d354", "server": "3784", "farm": 4, "title": "23-02-9", "isprimary": 1 }, { "id": "8877049006", "secret": "741139a946", "server": "5461", "farm": 6, "title": "23-02-7", "isprimary": 0 } ], "page": 1, "per_page": "500", "perpage": "500", "pages": 1, "total": 2, "title": "Enseñanza - Nombre album 9" }, "stat": "ok" }';

        $galleryId = 'gallery_id';
        $query     = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photosets.getPhotos',
            'photoset_id'    => $galleryId,
            'extras'         => 'tags, url_o',
            'privacy_filter' => null,
            'per_page'       => 100,
            'page'           => 1,
            'media'          => 'all',
        ];


        $this->http
            ->shouldReceive('setQuery')
            ->with($query)
            ->once();

        $this->http
            ->shouldReceive('GET')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('getBody')
            ->once()
            ->andReturn(json_decode($stringResponse));

        $photos = $this->photo->all($galleryId);

        assertThat($photos, is(anInstanceOf('Illuminate\\Support\\Collection')));
        assertThat($photos->id, is(nonEmptyString()));
        assertThat($photos->title, is(nonEmptyString()));
        assertThat($photos->description, is(nonEmptyString()));
        assertThat($photos->source, is(nonEmptyString()));
        assertThat($photos->source_thumbnail, is(nonEmptyString()));
        assertThat($photos->created, is(integerValue()));

    }



}
